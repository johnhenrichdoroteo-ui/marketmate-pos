<?php

use CodeIgniter\Boot;
use Config\Paths;

$root = dirname(__DIR__);
$writable = rtrim(sys_get_temp_dir(), '\\/') . '/marketmate-writable';

if (isset($_GET['__marketmate_health'])) {
    header('Content-Type: application/json');
    $readEnvironment = static function (string $name): string|false {
        $value = getenv($name);
        if ($value !== false && $value !== '') {
            return $value;
        }

        $value = $_ENV[$name] ?? $_SERVER[$name] ?? false;

        return is_string($value) ? $value : false;
    };
    $databaseUrl = $readEnvironment('MYSQL_PUBLIC_URL') ?: $readEnvironment('DATABASE_URL') ?: $readEnvironment('MYSQL_URL');
    $rawDatabaseUrl = is_string($databaseUrl) ? $databaseUrl : '';
    $databaseUrl = is_string($databaseUrl) ? trim($databaseUrl, " \t\n\r\0\x0B\"'") : $databaseUrl;
    $parts = is_string($databaseUrl) ? parse_url($databaseUrl) : false;
    if (! is_array($parts) && is_string($databaseUrl) && preg_match('~^(mysql|mariadb)://([^:]+):(.+)@([^:/]+):(\d+)/([^?]+)~', $databaseUrl, $matches)) {
        $parts = [
            'scheme' => $matches[1],
            'user' => $matches[2],
            'pass' => $matches[3],
            'host' => $matches[4],
            'port' => (int) $matches[5],
            'path' => '/' . $matches[6],
        ];
    }
    $environmentKeys = array_values(array_filter(
        array_unique(array_merge(array_keys($_ENV), array_keys($_SERVER))),
        static fn (string $key): bool => str_contains($key, 'MYSQL') || str_contains($key, 'DATABASE') || $key === 'CI_ENVIRONMENT',
    ));
    $status = [
        'version' => '2026-10-10.3',
        'mysqli' => extension_loaded('mysqli'),
        'database_url' => is_array($parts),
        'database' => false,
        'environment_keys' => $environmentKeys,
        'public_url_type' => get_debug_type($_ENV['MYSQL_PUBLIC_URL'] ?? $_SERVER['MYSQL_PUBLIC_URL'] ?? null),
        'public_url_length' => is_string($_ENV['MYSQL_PUBLIC_URL'] ?? $_SERVER['MYSQL_PUBLIC_URL'] ?? null)
            ? strlen($_ENV['MYSQL_PUBLIC_URL'] ?? $_SERVER['MYSQL_PUBLIC_URL'])
            : null,
        'mysql_scheme_position' => strpos($rawDatabaseUrl, 'mysql://'),
        'url_separator_position' => strpos($rawDatabaseUrl, '://'),
        'first_codepoint' => $rawDatabaseUrl !== '' ? ord($rawDatabaseUrl[0]) : null,
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
