#!/usr/bin/env python3
from ftplib import FTP
from io import BytesIO
import time

HOST, USER, PASS, SUB = "ftp.cadriciel.com", "cadri2834001", "bB4$AfxAgy2_PJ9", "kishala-trans.cadriciel.com"

PROBE = r'''<?php
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(FCPATH);

$steps = [];

function step($label, callable $fn) {
    global $steps;
    try {
        $result = $fn();
        $steps[] = "OK  $label => " . (is_scalar($result) ? $result : json_encode($result));
    } catch (Throwable $e) {
        $steps[] = "ERR $label => " . get_class($e) . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine();
    }
}

step('PHP', fn() => PHP_VERSION);

step('boot paths', function () {
    require FCPATH . 'home/app/Config/Paths.php';
    $paths = new Config\Paths();
    require $paths->systemDirectory . '/Boot.php';
    return 'loaded';
});

step('define CI', function () {
    require FCPATH . 'home/app/Config/Paths.php';
    $paths = new Config\Paths();
    require $paths->systemDirectory . '/Boot.php';
    // Minimal boot for CLI-like env load
    CodeIgniter\Boot::bootConsole($paths);
    return ENVIRONMENT;
});

step('db connect', function () {
    $db = Config\Database::connect();
    $db->initialize();
    return $db->getDatabase();
});

step('find serge', function () {
    $db = Config\Database::connect();
    $row = $db->table('utilisateur u')
        ->select('u.id_utilisateur,u.username,u.statut,r.code_role')
        ->join('role r', 'r.id_role = u.id_role')
        ->where('u.username', 'serge')
        ->where('u.deleted_at', null)
        ->get()->getRowArray();
    if (!$row) throw new RuntimeException('user not found');
    return json_encode($row);
});

step('password verify', function () {
    $db = Config\Database::connect();
    $row = $db->table('utilisateur')->where('username', 'serge')->where('deleted_at', null)->get()->getRowArray();
    if (!$row) throw new RuntimeException('user missing');
    $ok = password_verify('Kishala@26', (string)$row['mot_de_passe']);
    if (!$ok) throw new RuntimeException('password mismatch');
    return 'match';
});

step('jwt pair', function () {
    $db = Config\Database::connect();
    $row = $db->table('utilisateur u')
        ->select('u.*, r.code_role, r.libelle AS role_libelle')
        ->join('role r', 'r.id_role = u.id_role')
        ->where('u.username', 'serge')
        ->where('u.deleted_at', null)
        ->get()->getRowArray();
    $jwt = service('jwtService');
    $pair = $jwt->createTokenPair($row, 'probe', '127.0.0.1');
    return substr($pair['access_token'], 0, 20) . '...';
});

step('session write', function () {
    $s = Config\Services::session();
    $s->set('probe', 'ok');
    return session_id();
});

echo implode("\n", $steps), "\n";

'''

def one(op):
    for i in range(6):
        ftp = FTP()
        try:
            ftp.connect(HOST, 21, timeout=120)
            ftp.login(USER, PASS)
            ftp.set_pasv(True)
            ftp.cwd(SUB)
            r = op(ftp)
            ftp.quit()
            return r
        except Exception as e:
            print(i+1, e)
            try: ftp.close()
            except: pass
            time.sleep(2)
    raise SystemExit(1)

one(lambda f: f.storbinary('STOR _login_probe.php', BytesIO(PROBE.encode('utf-8'))))
print('uploaded _login_probe.php')

# fetch latest log tail
for log in ['home/writable/logs/log-2026-07-20.log']:
    try:
        b = BytesIO()
        one(lambda f,b=b,ln=log: f.retrbinary(f'RETR {ln}', b.write))
        text = b.getvalue().decode('utf-8', errors='replace')
        print('\n=== LOG TAIL ===')
        print('\n'.join(text.splitlines()[-60:]))
    except Exception as e:
        print('log fail', e)
