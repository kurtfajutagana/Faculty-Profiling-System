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
    $dbName = getenv('DB_NAME') ?: (getenv('MYSQLDATABASE') ?: 'finalproj');

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

    $conn = mysqli_init();
    if ($conn) {
        $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3);
        $connected = @$conn->real_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
        if ($connected && !$conn->connect_error) {
            $dbStatus = 'connected';
            $conn->close();
        } else {
            $dbStatus = 'disconnected';
            $dbMessage = 'Unable to connect to database host';
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
    'response_time_ms' => $responseTimeMs
], JSON_PRETTY_PRINT);
exit();
?>

