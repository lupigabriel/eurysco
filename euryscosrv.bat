setlocal enableextensions
cd /d "%~dp0"

wmic.exe os get osarchitecture | find "64"
if %errorlevel% equ 0 set osarc=x64
if %errorlevel% neq 0 set osarc=x86
ver.exe | find " 5."
if %errorlevel% equ 0 set osarc=x86&set osold="_xp2k3"&set sslprotocol=TLSv1
if %errorlevel% neq 0 set osold=""

if not exist "%cd%\ext\7zip.exe" copy "%cd%\installs\7z_%osarc%\7z.exe" "%cd%\ext\7zip.exe" /y
if not exist "%cd%\ext\7z.dll" copy "%cd%\installs\7z_%osarc%\7z.dll" "%cd%\ext\7z.dll" /y

set clr=0
if exist "%cd%\apache_%osarc%" if not exist "%cd%\apache" ren "%cd%\apache_%osarc%" "apache" & set clr=1
if exist "%cd%\apache_x64" rd "%cd%\apache_x64" /s /q
if exist "%cd%\apache_x86" rd "%cd%\apache_x86" /s /q
if exist "%cd%\php_%osarc%%osold:~1,-1%" if not exist "%cd%\php" ren "%cd%\php_%osarc%%osold:~1,-1%" "php" & set clr=1
if exist "%cd%\php_x64" rd "%cd%\php_x64" /s /q
if exist "%cd%\php_x86" rd "%cd%\php_x86" /s /q
if exist "%cd%\php_x86_xp2k3" rd "%cd%\php_x86_xp2k3" /s /q
if %clr% equ 1 cscript.exe "euryscoclr.vbs"

set servicename_last=%1
set servicename=%2
set servicestart=%3
set serviceuser=%4
set servicedisplay=%5
set serviceport=%6
set phpport=%7
set phpexe=%8
set relpath=%9

cd "%cd%"
if not exist "euryscosrv.exe" cd..

if %relpath:~1,-1% neq agent if %relpath:~1,-1% neq server if not exist "%cd%\cert\%phpexe:~1,-1%.crt" cd "%cd%\apache\bin" & openssl.exe req -x509 -nodes -days 1095 -newkey rsa:4096 -sha512 -keyout "..\..\cert\%phpexe:~1,-1%.key" -out "..\..\cert\%phpexe:~1,-1%.crt" -config "..\conf\openssl.cnf" -subj "/C=EU/ST=eurysco Any State/L=eurysco Any Locality/O=eurysco Any Organization/OU=eurysco/CN=%computername%" & openssl.exe req -out "..\..\cert\%phpexe:~1,-1%.csr" -key "..\..\cert\%phpexe:~1,-1%.key" -new -sha512 -config "..\conf\openssl.cnf" -subj "/C=EU/ST=eurysco Any State/L=eurysco Any Locality/O=eurysco Any Organization/OU=eurysco/CN=%computername%" & cd ..\..\
if %relpath:~1,-1% neq agent if %relpath:~1,-1% equ server if not exist "%cd%\cert\%phpexe:~1,-1%.crt" cd "%cd%\apache\bin" & openssl.exe req -x509 -nodes -days 365 -newkey rsa:2048 -sha512 -keyout "..\..\cert\%phpexe:~1,-1%.key" -out "..\..\cert\%phpexe:~1,-1%.crt" -config "..\conf\openssl.cnf" -subj "/C=EU/ST=eurysco Any State/L=eurysco Any Locality/O=eurysco Any Organization/OU=eurysco/CN=%computername%" & openssl.exe req -out "..\..\cert\%phpexe:~1,-1%.csr" -key "..\..\cert\%phpexe:~1,-1%.key" -new -sha512 -config "..\conf\openssl.cnf" -subj "/C=EU/ST=eurysco Any State/L=eurysco Any Locality/O=eurysco Any Organization/OU=eurysco/CN=%computername%" & cd ..\..\
if %relpath:~1,-1% neq agent echo Define SRVROOT "%cd%\apache">"%cd%\apache\conf\httpd_srvroot.conf"
if %relpath:~1,-1% neq agent echo Listen %serviceport:~1,-1%>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_port.conf"
if %relpath:~1,-1% neq agent echo ^<VirtualHost *:%serviceport:~1,-1%^>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	SSLProxyEngine On>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	SSLEngine On>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	SSLProtocol %sslprotocol%>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	ProxyPreserveHost On>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	ProxyRequests Off>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	SSLCertificateFile      ../cert/%phpexe:~1,-1%.crt>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	SSLCertificateKeyFile   ../cert/%phpexe:~1,-1%.key>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	^<Location /^>>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent if %phpexe:~9,-1% equ core echo 		ProxyPass http://127.0.0.1:%phpport:~1,-1%/ Timeout=180 KeepAlive=On>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent if %phpexe:~9,-1% equ executor echo 		ProxyPass http://127.0.0.1:%phpport:~1,-1%/ Timeout=2000 KeepAlive=On>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent if %phpexe:~9,-1% equ server echo 		ProxyPass http://127.0.0.1:%phpport:~1,-1%/ Timeout=35 KeepAlive=On>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 		ProxyPassReverse http://127.0.0.1:%phpport:~1,-1%/>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 		ProxyPreserveHost On>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 		SSLRequireSSL>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	^</Location^>>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo ^</VirtualHost^>>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent net.exe stop "%servicename_last:~1,-1%SSL"
if %relpath:~1,-1% neq agent taskkill.exe /f /im "httpd_%phpexe:~1,-1%.exe" /t
if %relpath:~1,-1% neq agent sc.exe delete "%servicename_last:~1,-1%SSL"
if %relpath:~1,-1% neq agent reg.exe delete "HKLM\SYSTEM\CurrentControlSet\services\%servicename_last:~1,-1%SSL" /f
if %relpath:~1,-1% neq agent netsh.exe firewall delete allowedprogram "%cd%\apache\bin\httpd_%phpexe:~1,-1%.exe" all
if %relpath:~1,-1% neq agent netsh.exe advfirewall firewall delete rule name=httpd_%phpexe:~1,-1% dir=in
if %relpath:~1,-1% neq agent sc.exe create "%servicename:~1,-1%SSL" start= "%servicestart:~1,-1%" binPath= "%cd%\euryscosrv.exe" obj= "%serviceuser:~1,-1%" DisplayName= "%servicedisplay:~1,-1% SSL"
if %relpath:~1,-1% neq agent reg.exe add "HKLM\SYSTEM\CurrentControlSet\services\%servicename:~1,-1%SSL\Parameters" /v "Application" /t REG_SZ /d "\"%cd%\apache\bin\httpd_%phpexe:~1,-1%.exe\" -f \"%cd%\apache\conf\httpd_%phpexe:~1,-1%.conf\"" /f
if %relpath:~1,-1% neq agent if %errorlevel% neq 0 cscript.exe "%cd%\euryscosrv.vbs" %servicename:~1,-1%SSL "@%cd%\apache\bin\httpd_%phpexe:~1,-1%.exe@ -f @%cd%\apache\conf\httpd_%phpexe:~1,-1%.conf@"
if %relpath:~1,-1% neq agent if not exist "%cd%\apache\bin\httpd_%phpexe:~1,-1%.exe" if exist "%cd%\apache\bin\httpd.exe" copy "%cd%\apache\bin\httpd.exe" "%cd%\apache\bin\httpd_%phpexe:~1,-1%.exe" /y
if %relpath:~1,-1% neq agent netsh.exe advfirewall firewall add rule name="httpd_%phpexe:~1,-1%" dir=in action=allow protocol=6 localport=%serviceport:~1,-1% program="%cd%\apache\bin\httpd_%phpexe:~1,-1%.exe" enable=yes
if %relpath:~1,-1% neq agent if %errorlevel% neq 0 netsh.exe firewall add allowedprogram "%cd%\apache\bin\httpd_%phpexe:~1,-1%.exe" "httpd_%phpexe:~1,-1%" enable

net.exe stop "%servicename_last:~1,-1%"
taskkill.exe /f /im "php_%phpexe:~1,-1%.exe" /t
if %relpath:~1,-1% equ agent taskkill.exe /f /im "eurysco.agent.status.check.exe" /im "eurysco.agent.exec.timeout.exe" /t
sc.exe delete "%servicename_last:~1,-1%"
reg.exe delete "HKLM\SYSTEM\CurrentControlSet\services\%servicename_last:~1,-1%" /f

sc.exe create "%servicename:~1,-1%" start= "%servicestart:~1,-1%" binPath= "%cd%\euryscosrv.exe" obj= "%serviceuser:~1,-1%" DisplayName= "%servicedisplay:~1,-1%"
if %relpath:~1,-1% neq agent reg.exe add "HKLM\SYSTEM\CurrentControlSet\services\%servicename:~1,-1%\Parameters" /v "Application" /t REG_SZ /d "\"%cd%\php\php_%phpexe:~1,-1%.exe\" -c \"%cd%\php\php.ini\" -t \"%cd%\%relpath:~1,-1%\" -S 127.0.0.1:%phpport:~1,-1%" /f
if %relpath:~1,-1% neq agent if %errorlevel% neq 0 cscript.exe "%cd%\euryscosrv.vbs" %servicename:~1,-1% "@%cd%\php\php_%phpexe:~1,-1%.exe@ -c @%cd%\php\php.ini@ -t @%cd%\%relpath:~1,-1%@ -S 127.0.0.1:%phpport:~1,-1%"
if %relpath:~1,-1% equ agent reg.exe add "HKLM\SYSTEM\CurrentControlSet\services\%servicename:~1,-1%\Parameters" /v "Application" /t REG_SZ /d "\"%cd%\php\php_%phpexe:~1,-1%.exe\" -c \"%cd%\php\php.ini\" \"%cd%\agent\conf\agent.init.php\"" /f
if %relpath:~1,-1% equ agent if %errorlevel% neq 0 cscript.exe "%cd%\euryscosrv.vbs" %servicename:~1,-1% "@%cd%\php\php_%phpexe:~1,-1%.exe@ -c @%cd%\php\php.ini@ @%cd%\agent\conf\agent.init.php@"

if %relpath:~1,-1% equ agent del "%cd%\agent\temp\agent.status" /f /q
if %relpath:~1,-1% equ server copy "%cd%\euryscoServer.default" "%cd%\sqlite\euryscoServer" /y
if %relpath:~1,-1% equ server if not exist "%cd%\sqlite\euryscoAudit" copy "%cd%\euryscoAudit.default" "%cd%\sqlite\euryscoAudit" /y

if %relpath:~1,-1% equ agent type "%cd%\agent\agent.inittop">"%cd%\agent\conf\agent.init.php"
if %relpath:~1,-1% equ agent echo session_save_path('%cd%\agent\temp'); session_start(); $_SESSION['agentpath'] = '%cd%\agent'; include($_SESSION['agentpath'] . '\\' . 'agent.php'); session_write_close();>>"%cd%\agent\conf\agent.init.php"
if %relpath:~1,-1% equ agent type "%cd%\agent\agent.initbot">>"%cd%\agent\conf\agent.init.php"

if not exist "%cd%\php\php.ini" if exist "%cd%\php.default_%osarc%%osold:~1,-1%" copy "%cd%\php.default_%osarc%%osold:~1,-1%" "%cd%\php\php.ini" /y
type "%cd%\php\php.ini" | find /i "error_log = " | find /i "logs\php_errors.log"
if %errorlevel% neq 0 echo error_log = "%cd%\php\logs\php_errors.log">>"%cd%\php\php.ini"
type "%cd%\php\php.ini" | find /i "upload_tmp_dir = " | find /i "temp"
if %errorlevel% neq 0 echo upload_tmp_dir = "%cd%\php\temp">>"%cd%\php\php.ini"
if not exist "%cd%\php\php_%phpexe:~1,-1%.exe" if exist "%cd%\php\php.exe" copy "%cd%\php\php.exe" "%cd%\php\php_%phpexe:~1,-1%.exe" /y

net.exe start "%servicename:~1,-1%"
if %relpath:~1,-1% neq agent net.exe start "%servicename:~1,-1%SSL"