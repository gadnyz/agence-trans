<?php
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(FCPATH);

require FCPATH . 'home/app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';

// Load env + autoloader without full web request
require $paths->systemDirectory . '/Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv($paths->envDirectory ?? $paths->appDirectory . '/../'))->load();
require $paths->systemDirectory . '/Common.php';
require FCPATH . 'home/vendor/autoload.php';
require $paths->systemDirectory . '/Config/AutoloadConfig.php';
require $paths->systemDirectory . '/Autoloader/Autoloader.php';
CodeIgniter\Autoloader\Autoloader::getInstance()->initialize(new Config\Autoload(), new Config\Modules());

echo "ENV=" . (defined('ENVIRONMENT') ? ENVIRONMENT : 'undef') . "\n";

try {
    $db = Config\Database::connect();
    $db->initialize();
    echo "DB=" . $db->getDatabase() . "\n";

    $user = (new App\Models\UserModel())->findActiveByUsername('serge');
    if (!$user) {
        echo "USER=not found\n";
        exit;
    }
    echo "USER=" . $user['username'] . " role=" . ($user['code_role'] ?? '?') . "\n";

    $ok = password_verify('Kishala@26', (string) $user['mot_de_passe']);
    echo "PWD=" . ($ok ? 'ok' : 'fail') . "\n";
    if (!$ok) exit;

    $jwt = service('jwtService');
    $pair = $jwt->createTokenPair($user, 'probe', '127.0.0.1');
    echo "JWT=ok exp=" . ($pair['expires_at'] ?? '?') . "\n";

    $public = $jwt->publicUser($user);
    echo "PUBLIC=" . json_encode($public['role']) . "\n";
} catch (Throwable $e) {
    echo "ERR=" . get_class($e) . ': ' . $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
}
