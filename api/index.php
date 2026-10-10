<?php

use CodeIgniter\Boot;
use Config\Paths;

$root = dirname(__DIR__);
$writable = rtrim(sys_get_temp_dir(), '\\/') . '/marketmate-writable';

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
