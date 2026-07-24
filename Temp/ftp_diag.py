#!/usr/bin/env python3
"""Diagnostic FTP LWS - lecture seule + upload probe minimal."""
from ftplib import FTP
from io import BytesIO
import sys

HOST = "ftp.cadriciel.com"
USER = "cadri2834001"
PASS = "bB4$AfxAgy2_PJ9"
SUB = "kishala-trans.cadriciel.com"


def connect():
    ftp = FTP()
    ftp.connect(HOST, 21, timeout=90)
    ftp.login(USER, PASS)
    ftp.set_pasv(True)
    ftp.cwd(SUB)
    return ftp


def size(ftp, path):
    try:
        return ftp.size(path)
    except Exception as e:
        return f"MISS ({e})"


def retr(ftp, path, limit=8000):
    buf = BytesIO()
    ftp.retrbinary(f"RETR {path}", buf.write)
    data = buf.getvalue()
    text = data.decode("utf-8", errors="replace")
    return text[:limit], len(data)


def main():
    ftp = connect()
    print("PWD:", ftp.pwd())

    files = [
        "index.php", ".htaccess", "home/.env", "home/.htaccess",
        "home/vendor/autoload.php",
        "home/vendor/codeigniter4/framework/system/Boot.php",
        "home/writable/logs",
        "_probe.php", "_boot_probe.php", "phpinfo.php",
    ]
    for f in files:
        print(f"SIZE {f}: {size(ftp, f)}")

    for f in [".htaccess", "index.php", "home/.env", "home/.htaccess"]:
        try:
            text, n = retr(ftp, f)
            print(f"\n===== {f} ({n} bytes) =====")
            print(text)
            print("===== END =====\n")
        except Exception as e:
            print(f"RETR FAIL {f}: {e}")

    # Minimal probe - no CI, just phpversion
    probe = b"<?php header('Content-Type: text/plain'); echo 'OK PHP=' . PHP_VERSION;\n"
    ftp.storbinary("STOR _diag.php", BytesIO(probe))
    print("Uploaded _diag.php")

    # Fix .htaccess - remove broken AddHandler if present
    try:
        ht, _ = retr(ftp, ".htaccess", limit=50000)
        if "x-httpd-php82" in ht or "x-httpd-php8" in ht:
            print("\nWARNING: .htaccess contains PHP handler override - may cause 500")
            # restore clean htaccess from project
            clean = open(r"C:\xampp\htdocs\kashala-trans-api\public\.htaccess", encoding="utf-8").read()
            ftp.storbinary("STOR .htaccess", BytesIO(clean.encode("utf-8")))
            print("Restored clean .htaccess from repo")
    except Exception as e:
        print("htaccess fix skip:", e)

    ftp.quit()
    print("DONE")


if __name__ == "__main__":
    main()
