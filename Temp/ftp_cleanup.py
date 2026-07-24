#!/usr/bin/env python3
"""Nettoyage post-diagnostic LWS : supprimer fichiers orphelins sur le serveur."""
from ftplib import FTP
import time

HOST, USER, PASS, SUB = "ftp.cadriciel.com", "cadri2834001", "bB4$AfxAgy2_PJ9", "kishala-trans.cadriciel.com"
TO_DELETE = ["app.php", "_diag.php", "_boot.php", "_probe.php", "_boot_probe.php", "phpinfo.php"]


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
            print(f"retry {i+1}: {e}")
            try:
                ftp.close()
            except Exception:
                pass
            time.sleep(2)
    raise SystemExit(1)


for name in TO_DELETE:
    try:
        one(lambda f, n=name: f.delete(n))
        print("DELETED", name)
    except Exception as e:
        print("SKIP", name, e)

# production .env (keep existing secrets, only fix environment flag)
def fix_env(ftp):
    from io import BytesIO
    buf = BytesIO()
    ftp.retrbinary("RETR home/.env", buf.write)
    env = buf.getvalue().decode("utf-8", errors="replace")
    env = env.replace("CI_ENVIRONMENT = development", "CI_ENVIRONMENT = production")
    env = env.replace("CI_ENVIRONMENT =development", "CI_ENVIRONMENT = production")
    if "app.forceGlobalSecureRequests = false" in env:
        env = env.replace("app.forceGlobalSecureRequests = false", "app.forceGlobalSecureRequests = true")
    ftp.storbinary("STOR home/.env", BytesIO(env.encode("utf-8")))
    print("ENV updated to production")

one(fix_env)
print("DONE")
