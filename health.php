<?php
/**
 * Health Check & Keep-Alive Endpoint
 * Designed for UptimeRobot, Render health checks, and monitoring services.
 * Keeps Render free-tier web services awake 24/7.
 */

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Access-Control-Allow-Origin: *');

$startTime = microtime(true);
$dbStatus = 'untested';
$dbMessage = '';

// Optional database ping (can be disabled via ?db=0 for ultra-fast response)
$checkDb = !isset($_GET['db']) || $_GET['db'] !== '0';

if ($checkDb) {
    // Attempt DB connection without dying on failure
    $dbHost = getenv('DB_HOST') ?: (getenv('MYSQLHOST') ?: 'localhost');
    $dbPort = (int)(getenv('DB_PORT') ?: (getenv('MYSQLPORT') ?: 3306));
    $dbUser = getenv('DB_USER') ?: (getenv('MYSQLUSER') ?: 'root');
    $dbPass = getenv('DB_PASSWORD') ?: (getenv('DB_PASS') ?: (getenv('MYSQLPASSWORD') ?: ''));
    $dbName = getenv('DB_NAME') ?: (getenv('MYSQLDATABASE') ?: 'test');

    // Check for DATABASE_URL format
    $dbUrl = getenv('DATABASE_URL') ?: getenv('MYSQL_URL');
    if ($dbUrl) {
        $parts = parse_url($dbUrl);
        $dbHost = $parts['host'] ?? $dbHost;
        $dbPort = isset($parts['port']) ? (int)$parts['port'] : $dbPort;
        $dbUser = $parts['user'] ?? $dbUser;
        $dbPass = $parts['pass'] ?? $dbPass;
        $dbName = isset($parts['path']) ? ltrim($parts['path'], '/') : $dbName;
    }

    $isRemote = ($dbHost !== 'localhost' && $dbHost !== '127.0.0.1');

    $conn = mysqli_init();
    if ($conn) {
        $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);
        $flags = 0;
        if ($isRemote) {
            $caBundle = file_exists('/etc/ssl/certs/ca-certificates.crt') ? '/etc/ssl/certs/ca-certificates.crt' : NULL;
            $conn->ssl_set(NULL, NULL, $caBundle, NULL, NULL);
            $flags = MYSQLI_CLIENT_SSL;
        }

        mysqli_report(MYSQLI_REPORT_OFF);
        try {
            $connected = @$conn->real_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort, NULL, $flags);
            if ($connected && !$conn->connect_error) {
                $dbStatus = 'connected';
                $conn->close();
            } else {
                $dbStatus = 'disconnected';
                $dbMessage = $conn->connect_error ?: 'Connection failed';
            }
        } catch (Throwable $e) {
            $dbStatus = 'disconnected';
            $dbMessage = $e->getMessage();
        }
    }
}

$responseTimeMs = round((microtime(true) - $startTime) * 1000, 2);

http_response_code(200);
echo json_encode([
    'status' => 'healthy',
    'service' => 'PLP Faculty Profiling System',
    'timestamp' => date('c'),
    'uptime_monitor' => 'ready',
    'database' => $dbStatus,
    'db_message' => $dbMessage ?: null,
    'response_time_ms' => $responseTimeMs
], JSON_PRETTY_PRINT);
exit();
?>
