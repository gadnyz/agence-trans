#!/usr/bin/env python3
from ftplib import FTP
from io import BytesIO
import time

HOST, USER, PASS, SUB = "ftp.cadriciel.com", "cadri2834001", "bB4$AfxAgy2_PJ9", "kishala-trans.cadriciel.com"
HTACCESS = r"""# LWS — document root du sous-domaine (kishala-trans.cadriciel.com)
# IMPORTANT : router uniquement vers index.php (jamais app.php).

Options -Indexes

<IfModule mod_rewrite.c>
	Options +FollowSymlinks
	RewriteEngine On

	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteCond %{REQUEST_URI} (.+)/$
	RewriteRule ^ %1 [L,R=301]

	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	# LWS: index.php/$1 => HTTP 500. Utiliser query-string pour PATH_INFO.
	RewriteRule ^(.*)$ index.php?/$1 [L,QSA]

	RewriteCond %{HTTP:Authorization} .
	RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
</IfModule>

<IfModule !mod_rewrite.c>
	ErrorDocument 404 index.php
</IfModule>

ServerSignature Off
"""


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


def size(path):
    def op(ftp):
        try:
            return ftp.size(path)
        except Exception as e:
            return str(e)
    return one(op)


# fix htaccess only
one(lambda f: f.storbinary("STOR .htaccess", BytesIO(HTACCESS.encode("utf-8"))))
print("FIXED .htaccess")
