<?php
header('Content-Type: text/plain; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '1');

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
$envFile = FCPATH . 'home/.env';
$env = parse_ini_file($envFile, false, INI_SCANNER_RAW);
if (!$env) {
    // CI .env uses "key = value" format - parse manually
    $env = [];
    foreach (file($envFile) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (str_contains($line, '=')) {
            [$k, $v] = array_map('trim', explode('=', $line, 2));
            $env[$k] = trim($v, " '\"");
        }
    }
}

$host = $env['database.default.hostname'] ?? 'localhost';
$dbname = $env['database.default.database'] ?? '';
$user = $env['database.default.username'] ?? '';
$pass = $env['database.default.password'] ?? '';
$port = (int) ($env['database.default.port'] ?? 3306);

echo "DB cfg: $user@$host:$port/$dbname\n";

$mysqli = new mysqli($host, $user, $pass, $dbname, $port);
if ($mysqli->connect_error) {
    die('CONNECT ERR: ' . $mysqli->connect_error);
}
echo "CONNECT ok\n";

$sql = "SELECT u.*, r.code_role FROM utilisateur u JOIN role r ON r.id_role=u.id_role WHERE u.username='serge' AND u.deleted_at IS NULL LIMIT 1";
$res = $mysqli->query($sql);
$row = $res ? $res->fetch_assoc() : null;
if (!$row) {
    die("USER serge not found\n");
}
echo "USER found id={$row['id_utilisateur']} role={$row['code_role']}\n";

$pwdOk = password_verify('Kishala@26', (string) $row['mot_de_passe']);
echo 'PWD ' . ($pwdOk ? 'ok' : 'FAIL hash=' . substr($row['mot_de_passe'], 0, 20)) . "\n";

$res2 = $mysqli->query("SHOW TABLES LIKE 'auth_refresh_tokens'");
echo 'TABLE auth_refresh_tokens ' . ($res2 && $res2->num_rows ? 'exists' : 'MISSING') . "\n";

if ($pwdOk) {
    $hash = hash('sha256', bin2hex(random_bytes(16)));
    $stmt = $mysqli->prepare("INSERT INTO auth_refresh_tokens (id_utilisateur, token_hash, issued_at, expires_at, user_agent, ip_address) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 'probe', '127.0.0.1')");
    if (!$stmt) {
        echo 'PREPARE ERR: ' . $mysqli->error . "\n";
    } else {
        $uid = (int) $row['id_utilisateur'];
        $stmt->bind_param('is', $uid, $hash);
        if ($stmt->execute()) {
            echo "INSERT refresh token ok id=" . $mysqli->insert_id . "\n";
            $mysqli->query("DELETE FROM auth_refresh_tokens WHERE id_refresh_token=" . (int) $mysqli->insert_id);
        } else {
            echo 'INSERT ERR: ' . $stmt->error . "\n";
        }
    }
}

$mysqli->close();
