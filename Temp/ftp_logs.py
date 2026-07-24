#!/usr/bin/env python3
from ftplib import FTP
from io import BytesIO
import time, glob

HOST, USER, PASS, SUB = "ftp.cadriciel.com", "cadri2834001", "bB4$AfxAgy2_PJ9", "kishala-trans.cadriciel.com"

def one(op):
    for i in range(6):
        ftp = FTP()
        try:
            ftp.connect(HOST, 21, timeout=120)
            ftp.login(USER, PASS)
            ftp.set_pasv(True)
            ftp.cwd(SUB)
            return op(ftp)
        except Exception as e:
            print(i+1, e)
            try: ftp.close()
            except: pass
            time.sleep(2)
    return None

# fix env
def fix(ftp):
    b=BytesIO(); ftp.retrbinary('RETR home/.env', b.write)
    env=b.getvalue().decode()
    env=env.replace('CI_ENVIRONMENT = development','CI_ENVIRONMENT = production')
    env=env.replace('CI_ENVIRONMENT =development','CI_ENVIRONMENT = production')
    env=env.replace('app.forceGlobalSecureRequests = false','app.forceGlobalSecureRequests = true')
    ftp.storbinary('STOR home/.env', BytesIO(env.encode()))
    print('env fixed')
    print(env)

one(fix)

# try read latest log
for logname in ['home/writable/logs/log-2026-07-20.log','home/writable/logs/log-2026-07-19.log']:
    try:
        b=BytesIO()
        one(lambda f,b=b,ln=logname: f.retrbinary(f'RETR {ln}', b.write))
        text=b.getvalue().decode(errors='replace')
        print(f'\n=== {logname} tail ===\n')
        print('\n'.join(text.splitlines()[-40:]))
    except Exception as e:
        print('no', logname, e)
