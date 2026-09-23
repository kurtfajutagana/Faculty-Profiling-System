<?php
/**
 * Database Connection Handler
 * Supports local XAMPP/WAMP, Docker, and Cloud MySQL (TiDB Cloud, Aiven, Railway, Render)
 */

// Helper to get env variable from $_ENV, $_SERVER, or getenv()
if (!function_exists('getDbEnv')) {
    function getDbEnv($key, $default = '') {
        if (!empty($_ENV[$key])) return $_ENV[$key];
        if (!empty($_SERVER[$key])) return $_SERVER[$key];
        $val = getenv($key);
        return ($val !== false && $val !== '') ? $val : $default;
    }
}

// 1. Check for standard DATABASE_URL / MYSQL_URL (e.g., mysql://user:pass@host:port/dbname)
$databaseUrl = getDbEnv('DATABASE_URL', getDbEnv('MYSQL_URL', getDbEnv('JAWSDB_URL', getDbEnv('CLEARDB_DATABASE_URL', ''))));

if (!empty($databaseUrl)) {
    $dbParts = parse_url($databaseUrl);
    $servername = $dbParts['host'] ?? 'localhost';
    $port       = isset($dbParts['port']) ? (int)$dbParts['port'] : 3306;
    $username   = $dbParts['user'] ?? 'root';
    $password   = $dbParts['pass'] ?? '';
    $dbname     = isset($dbParts['path']) ? ltrim($dbParts['path'], '/') : 'test';
} else {
    // 2. Read discrete environment variables with local defaults
    $servername = getDbEnv('DB_HOST', getDbEnv('MYSQLHOST', 'localhost'));
    $port       = (int)getDbEnv('DB_PORT', getDbEnv('MYSQLPORT', 3306));
    $username   = getDbEnv('DB_USER', getDbEnv('MYSQLUSER', 'root'));
    $password   = getDbEnv('DB_PASSWORD', getDbEnv('DB_PASS', getDbEnv('MYSQLPASSWORD', '')));
    $dbname     = getDbEnv('DB_NAME', getDbEnv('MYSQLDATABASE', 'test'));
}

$sslCa = getDbEnv('DB_SSL_CA', getDbEnv('MYSQL_SSL_CA', ''));

// 3. Establish Connection
global $conn;
$conn = mysqli_init();

if (!$conn) {
    die("mysqli_init failed");
}

// Set connection timeout (5 seconds)
$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);

// Detect if connecting to remote/cloud host (TiDB Cloud, Aiven, etc.)
$isRemoteHost = ($servername !== 'localhost' && $servername !== '127.0.0.1');

// Locate system CA bundle if available
$caBundle = NULL;
if (!empty($sslCa) && file_exists($sslCa)) {
    $caBundle = $sslCa;
} elseif (file_exists('/etc/ssl/certs/ca-certificates.crt')) {
    $caBundle = '/etc/ssl/certs/ca-certificates.crt';
} elseif (file_exists('/etc/pki/tls/certs/ca-bundle.crt')) {
    $caBundle = '/etc/pki/tls/certs/ca-bundle.crt';
}

$flags = 0;
if ($isRemoteHost || !empty($sslCa)) {
    if (!empty($caBundle)) {
        $conn->ssl_set(NULL, NULL, $caBundle, NULL, NULL);
    } else {
        $conn->ssl_set(NULL, NULL, NULL, NULL, NULL);
    }
    $flags = MYSQLI_CLIENT_SSL;
}

// Disable default PHP 8.1 exception throwing during connection attempt
mysqli_report(MYSQLI_REPORT_OFF);

$connected = false;
$errorMsg = '';

try {
    $connected = @$conn->real_connect($servername, $username, $password, $dbname, $port, NULL, $flags);
    if (!$connected) {
        $errorMsg = $conn->connect_error ?: mysqli_connect_error();
    }
} catch (Throwable $e) {
    $connected = false;
    $errorMsg = $e->getMessage();
}

if (!$connected) {
    error_log("Database connection error: " . $errorMsg);
    
    // If request expects JSON (API endpoints), respond with JSON
    $isJson = (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
           || (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
           || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
           
    if ($isJson) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Database connection failed. Please verify database settings and status.',
            'details' => (getDbEnv('APP_DEBUG', 'false') === 'true') ? $errorMsg : null
        ]);
        exit();
    }
    
    // Otherwise show friendly error page
    http_response_code(500);
    die("<h3>Database Connection Error</h3><p>Unable to connect to the database: " . htmlspecialchars($errorMsg) . "</p>");
}

// Set UTF-8 charset
$conn->set_charset("utf8mb4");
?>