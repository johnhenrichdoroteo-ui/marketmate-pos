<?php

use CodeIgniter\Boot;
use Config\Paths;

$root = dirname(__DIR__);
$writable = rtrim(sys_get_temp_dir(), '\\/') . '/marketmate-writable';

if (($_SERVER['REQUEST_URI'] ?? '') === '/__marketmate_health') {
    header('Content-Type: application/json');
    $databaseUrl = getenv('MYSQL_PUBLIC_URL') ?: getenv('DATABASE_URL') ?: getenv('MYSQL_URL');
    $parts = is_string($databaseUrl) ? parse_url($databaseUrl) : false;
    $status = [
        'mysqli' => extension_loaded('mysqli'),
        'database_url' => is_array($parts),
        'database' => false,
    ];

    if ($status['mysqli'] && is_array($parts)) {
        mysqli_report(MYSQLI_REPORT_OFF);
        $connection = mysqli_init();
        $connected = $connection->real_connect(
            $parts['host'] ?? '',
            isset($parts['user']) ? urldecode($parts['user']) : '',
            isset($parts['pass']) ? urldecode($parts['pass']) : '',
            isset($parts['path']) ? ltrim($parts['path'], '/') : '',
            (int) ($parts['port'] ?? 3306),
        );
        $status['database'] = $connected;
        if (! $connected) {
            $status['error_code'] = mysqli_connect_errno();
            $status['error'] = mysqli_connect_error();
        }
    }

    http_response_code($status['database'] ? 200 : 503);
    echo json_encode($status);
    exit;
}

foreach (['', '/cache', '/debugbar', '/logs', '/session', '/uploads'] as $directory) {
    $path = $writable . $directory;
    if (! is_dir($path)) {
        mkdir($path, 0775, true);
    }
}

$host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? null;
$proto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? (! empty($_SERVER['HTTPS']) ? 'https' : 'http');
if (is_string($host) && $host !== '' && getenv('APP_BASE_URL') === false) {
    putenv('app.baseURL=' . $proto . '://' . $host . '/');
}
if (($environment = getenv('CI_ENVIRONMENT')) === false || $environment === '') {
    putenv('CI_ENVIRONMENT=production');
}
putenv('cookie.secure=true');

define('FCPATH', $root . '/public/');
chdir(FCPATH);

require $root . '/app/Config/Paths.php';
$paths = new Paths();

require $paths->systemDirectory . '/Boot.php';
exit(Boot::bootWeb($paths));
