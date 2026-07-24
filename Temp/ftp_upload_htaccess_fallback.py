#!/usr/bin/env python3
from ftplib import FTP
from io import BytesIO
from pathlib import Path

HOST, USER, PASS, SUB = "ftp.cadriciel.com", "cadri2834001", "bB4$AfxAgy2_PJ9", "kishala-trans.cadriciel.com"
HTACCESS = Path(__file__).with_name("lws_htaccess_fallback.txt").read_text(encoding="utf-8")

ftp = FTP()
ftp.connect(HOST, 21, timeout=90)
ftp.login(USER, PASS)
ftp.set_pasv(True)
ftp.cwd(SUB)
ftp.storbinary("STOR .htaccess", BytesIO(HTACCESS.encode("utf-8")))
ftp.quit()
print("uploaded fallback .htaccess")
