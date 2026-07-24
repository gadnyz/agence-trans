#!/usr/bin/env python3
"""Deploy web auth fixes to LWS via FTP."""
from ftplib import FTP
from io import BytesIO
import os
import time

HOST = "ftp.cadriciel.com"
USER = "cadri2834001"
PASS = "bB4$AfxAgy2_PJ9"
SUB = "kishala-trans.cadriciel.com"
ROOT = r"C:\xampp\htdocs\kashala-trans-api"

FILES = [
    ("app/Controllers/Web/AuthController.php", "home/app/Controllers/Web/AuthController.php"),
    ("app/Controllers/Web/BaseWebController.php", "home/app/Controllers/Web/BaseWebController.php"),
    ("app/Controllers/Web/PlanningController.php", "home/app/Controllers/Web/PlanningController.php"),
    ("deploy/lws/public/.htaccess", ".htaccess"),
    ("deploy/lws/public/index.php", "index.php"),
]


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
    local = os.path.join(ROOT, local_rel.replace("/", os.sep))
    with open(local, "rb") as f:
        data = f.read()

    def op(ftp):
        ftp.storbinary(f"STOR {remote}", BytesIO(data))
        return len(data)

    n = one(op)
    print(f"OK {remote} ({n} bytes)")


def main():
    for local, remote in FILES:
        upload(local, remote)
    print("DEPLOY DONE")


if __name__ == "__main__":
    main()
