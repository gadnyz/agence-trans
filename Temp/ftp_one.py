#!/usr/bin/env python3
from ftplib import FTP
from io import BytesIO
HOST, USER, PASS, SUB = "ftp.cadriciel.com", "cadri2834001", "bB4$AfxAgy2_PJ9", "kishala-trans.cadriciel.com"
local = r"C:\xampp\htdocs\kashala-trans-api\Temp\login_probe_db.php"
remote = "_login_db.php"
ftp = FTP(); ftp.connect(HOST, 21, timeout=120); ftp.login(USER, PASS); ftp.set_pasv(True); ftp.cwd(SUB)
ftp.storbinary(f"STOR {remote}", BytesIO(open(local,'rb').read())); ftp.quit(); print('uploaded')
