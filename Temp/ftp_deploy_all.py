#!/usr/bin/env python3
from ftplib import FTP
from io import BytesIO
import os, time

HOST = "ftp.cadriciel.com"
USER = "cadri2834001"
PASS = "bB4$AfxAgy2_PJ9"
SUB = "kishala-trans.cadriciel.com"
ROOT = r"C:\xampp\htdocs\kashala-trans-api"

FILES = [
    ("app/Config/Filters.php", "home/app/Config/Filters.php"),
    ("app/Controllers/Web/AuthController.php", "home/app/Controllers/Web/AuthController.php"),
    ("app/Controllers/Web/BaseWebController.php", "home/app/Controllers/Web/BaseWebController.php"),
    ("app/Controllers/Web/PlanningController.php", "home/app/Controllers/Web/PlanningController.php"),
]

PWD_HASH = "$2y$10$DiGPaxpvGcgIkcDYJum.segx.DjGpGb6OxOV1zSweNewrZ1h.2Iua"

RESET_SQL = f"""UPDATE utilisateur
SET mot_de_passe = '{PWD_HASH}', statut = 'Actif', deleted_at = NULL
WHERE username = 'serge';
"""

RESET_PHP = f'''<?php
header('Content-Type: text/plain; charset=utf-8');
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
$env = file_get_contents(FCPATH . 'home/.env');
$cfg = [];
foreach (explode("\\n", $env) as $line) {{
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    if (str_contains($line, '=')) {{
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        $cfg[$k] = trim($v, " '\\"");
    }}
}}
$mysqli = new mysqli($cfg['database.default.hostname'], $cfg['database.default.username'], $cfg['database.default.password'], $cfg['database.default.database'], (int)($cfg['database.default.port'] ?? 3306));
if ($mysqli->connect_error) die('connect err: ' . $mysqli->connect_error);
$sql = "{RESET_SQL.replace(chr(10), ' ')}";
if (!$mysqli->query($sql)) die('update err: ' . $mysqli->error);
echo "updated rows=" . $mysqli->affected_rows . "\\n";
$res = $mysqli->query("SELECT username, statut FROM utilisateur WHERE username='serge' LIMIT 1");
$row = $res->fetch_assoc();
echo json_encode($row) . "\\n";
'''


def one(op):
    last = None
    for i in range(8):
        ftp = FTP()
        try:
            ftp.connect(HOST, 21, timeout=120)
            ftp.login(USER, PASS)
            ftp.set_pasv(True)
            ftp.cwd(SUB)
            r = op(ftp)
            try:
                ftp.quit()
            except Exception:
                ftp.close()
            return r
        except Exception as e:
            last = e
            print(f"retry {i+1}: {e}")
            try:
                ftp.close()
            except Exception:
                pass
            time.sleep(3)
    raise last


def upload(local_rel, remote):
    data = open(os.path.join(ROOT, local_rel.replace('/', os.sep)), 'rb').read()
    one(lambda f: f.storbinary(f"STOR {remote}", BytesIO(data)))
    print(f"OK {remote}")


for local, remote in FILES:
    upload(local, remote)

one(lambda f: f.storbinary("STOR _reset_serge.php", BytesIO(RESET_PHP.encode('utf-8'))))
print("OK _reset_serge.php")

# temp dev env for visible errors
def fix_env(ftp):
    b = BytesIO()
    ftp.retrbinary("RETR home/.env", b.write)
    env = b.getvalue().decode('utf-8', errors='replace')
    env = env.replace('CI_ENVIRONMENT = production', 'CI_ENVIRONMENT = development')
    ftp.storbinary("STOR home/.env", BytesIO(env.encode('utf-8')))
    print('env -> development (temp)')

one(fix_env)
print('DONE')
