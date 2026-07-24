<?php
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');
register_shutdown_function(function () {
    $e = error_get_last();
    if ($e) {
        echo "\nFATAL: {$e['message']} @ {$e['file']}:{$e['line']}\n";
    }
});

echo "PHP=" . PHP_VERSION . "\n";
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(FCPATH);

try {
    require FCPATH . 'home/app/Config/Paths.php';
    $paths = new Config\Paths();
    require $paths->systemDirectory . '/Boot.php';
    echo "Boot loaded\n";
    CodeIgniter\Boot::bootWeb($paths);
} catch (Throwable $e) {
    echo "BOOT ERR: " . get_class($e) . ': ' . $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
