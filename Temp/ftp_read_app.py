#!/usr/bin/env python3
from ftplib import FTP
from io import BytesIO
import time
HOST, USER, PASS, SUB = "ftp.cadriciel.com", "cadri2834001", "bB4$AfxAgy2_PJ9", "kishala-trans.cadriciel.com"

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

def retr(p):
    b=BytesIO()
    one(lambda f: f.retrbinary(f'RETR {p}', b.write))
    return b.getvalue().decode()

print(retr('app.php'))
