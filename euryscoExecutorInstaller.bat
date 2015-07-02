setlocal enableextensions
cd /d "%~dp0"

wmic.exe os get osarchitecture | find "64"
if %errorlevel% equ 0 set osarc=x64
if %errorlevel% neq 0 set osarc=x86
ver.exe | find " 5."
if %errorlevel% equ 0 set osarc=x86&set osold="_xp2k3"
if %errorlevel% neq 0 set osold=""

set servicename_last="eurysco"
set servicename="euryscoExecutor"
set servicestart="auto"
set serviceuser="LocalSystem"
set servicedisplay="eurysco Executor"
if [%1] equ [] set serviceport="59981"
if [%1] neq [] set serviceport="%1"
if [%2] equ [] set phpport="59971"
if [%2] neq [] set phpport="%2"
set phpexe="eurysco_executor"
set relpath="core"
set relname="executor"

set sslprotocol=TLSv1.2
if %osold% equ "_xp2k3" set sslprotocol=TLSv1

echo ^<?xml version="1.0"?^>>.\%relpath:~1,-1%\conf\config_%relname:~1,-1%.xml
echo ^<config^>>>.\%relpath:~1,-1%\conf\config_%relname:~1,-1%.xml
echo 	^<settings^>>>.\%relpath:~1,-1%\conf\config_%relname:~1,-1%.xml
echo 		^<%relname:~1,-1%servicedisplayname^>%servicedisplay:~1,-1%^</%relname:~1,-1%servicedisplayname^>>>.\%relpath:~1,-1%\conf\config_%relname:~1,-1%.xml
echo 		^<%relname:~1,-1%servicename^>%servicename:~1,-1%^</%relname:~1,-1%servicename^>>>.\%relpath:~1,-1%\conf\config_%relname:~1,-1%.xml
echo 		^<%relname:~1,-1%servicestartuptype^>%servicestart:~1,-1%^</%relname:~1,-1%servicestartuptype^>>>.\%relpath:~1,-1%\conf\config_%relname:~1,-1%.xml
echo 		^<%relname:~1,-1%servicelogonas^>%serviceuser:~1,-1%^</%relname:~1,-1%servicelogonas^>>>.\%relpath:~1,-1%\conf\config_%relname:~1,-1%.xml
echo 		^<%relname:~1,-1%listeningport^>%serviceport:~1,-1%^</%relname:~1,-1%listeningport^>>>.\%relpath:~1,-1%\conf\config_%relname:~1,-1%.xml
echo 		^<%relname:~1,-1%phpport^>%phpport:~1,-1%^</%relname:~1,-1%phpport^>>>.\%relpath:~1,-1%\conf\config_%relname:~1,-1%.xml
echo 	^</settings^>>>.\%relpath:~1,-1%\conf\config_%relname:~1,-1%.xml
echo ^</config^>>>.\%relpath:~1,-1%\conf\config_%relname:~1,-1%.xml

if not exist "%cd%\cert\%phpexe:~1,-1%.crt" cd "%cd%\apache_%osarc%\bin" & openssl.exe req -x509 -nodes -days 1825 -newkey rsa:2048 -sha512 -keyout "..\..\cert\%phpexe:~1,-1%.key" -out "..\..\cert\%phpexe:~1,-1%.crt" -config "..\conf\openssl.cnf" -subj "/C=EU/ST=eurysco Any State/L=eurysco Any Locality/O=eurysco Any Organization/OU=eurysco/CN=%computername%" & cd ..\..\
rem certutil.exe -addstore "Root" "%cd%\cert\%phpexe:~1,-1%.crt"

echo Define SRVROOT "%cd%\apache_%osarc%">"%cd%\apache_%osarc%\conf\httpd_srvroot.conf"
echo Listen %serviceport:~1,-1%>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_port.conf"
echo ^<VirtualHost *:%serviceport:~1,-1%^>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	SSLProxyEngine On>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	SSLEngine On>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	SSLProtocol %sslprotocol%>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	ProxyPreserveHost On>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	ProxyRequests Off>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	SSLCertificateFile      ../cert/%phpexe:~1,-1%.crt>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	SSLCertificateKeyFile   ../cert/%phpexe:~1,-1%.key>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	^<Location /^>>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 		ProxyPass http://127.0.0.1:%phpport:~1,-1%/ Timeout=300 KeepAlive=On>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 		ProxyPassReverse http://127.0.0.1:%phpport:~1,-1%/>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 		ProxyPreserveHost On>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 		SSLRequireSSL>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	^</Location^>>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo ^</VirtualHost^>>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
net.exe stop "%servicename_last:~1,-1%SSL"
taskkill.exe /f /im "httpd_%phpexe:~1,-1%.exe" /t
sc.exe delete "%servicename_last:~1,-1%SSL"
reg.exe delete "HKLM\SYSTEM\CurrentControlSet\services\%servicename_last:~1,-1%SSL" /f
netsh.exe firewall delete allowedprogram "%cd%\apache_%osarc%\bin\httpd_%phpexe:~1,-1%.exe" all
netsh.exe advfirewall firewall delete rule name=httpd_%phpexe:~1,-1% dir=in
sc.exe create "%servicename:~1,-1%SSL" start= "%servicestart:~1,-1%" binPath= "%cd%\euryscosrv.exe" obj= "%serviceuser:~1,-1%" DisplayName= "%servicedisplay:~1,-1% SSL"
reg.exe add "HKLM\SYSTEM\CurrentControlSet\services\%servicename:~1,-1%SSL\Parameters" /v "Application" /t REG_SZ /d "\"%cd%\apache_%osarc%\bin\httpd_%phpexe:~1,-1%.exe\" -f \"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%.conf\"" /f
if not exist "%cd%\apache_%osarc%\bin\httpd_%phpexe:~1,-1%.exe" if exist "%cd%\apache_%osarc%\bin\httpd.exe" copy "%cd%\apache_%osarc%\bin\httpd.exe" "%cd%\apache_%osarc%\bin\httpd_%phpexe:~1,-1%.exe" /y
netsh.exe advfirewall firewall add rule name="httpd_%phpexe:~1,-1%" dir=in action=allow protocol=6 localport=%serviceport:~1,-1% remoteport=%phpport:~1,-1% program="%cd%\apache_%osarc%\bin\httpd_%phpexe:~1,-1%.exe" enable=yes
if %errorlevel% neq 0 netsh.exe firewall add allowedprogram "%cd%\apache_%osarc%\bin\httpd_%phpexe:~1,-1%.exe" "httpd_%phpexe:~1,-1%" enable
net.exe start "%servicename:~1,-1%SSL"

net.exe stop "%servicename_last:~1,-1%"
taskkill.exe /f /im "%phpexe:~1,-1%.exe" /t
sc.exe delete "%servicename_last:~1,-1%"
reg.exe delete "HKLM\SYSTEM\CurrentControlSet\services\%servicename_last:~1,-1%" /f

sc.exe create "%servicename:~1,-1%" start= "%servicestart:~1,-1%" binPath= "%cd%\euryscosrv.exe" obj= "%serviceuser:~1,-1%" DisplayName= "%servicedisplay:~1,-1%"
reg.exe add "HKLM\SYSTEM\CurrentControlSet\services\%servicename:~1,-1%\Parameters" /v "Application" /t REG_SZ /d "\"%cd%\php_%osarc%%osold:~1,-1%\php_%phpexe:~1,-1%.exe\" -c \"%cd%\php_%osarc%%osold:~1,-1%\php.ini\" -t \"%cd%\%relpath:~1,-1%\" -S 127.0.0.1:%phpport:~1,-1%" /f

if not exist "%cd%\php_%osarc%%osold:~1,-1%\php.ini" if exist "%cd%\php.default_%osarc%%osold:~1,-1%" copy "%cd%\php.default_%osarc%%osold:~1,-1%" "%cd%\php_%osarc%%osold:~1,-1%\php.ini" /y
type "%cd%\php_%osarc%%osold:~1,-1%\php.ini" | find /i "error_log = " | find /i "logs\php_errors.log"
if %errorlevel% neq 0 echo error_log = "%cd%\php_%osarc%%osold:~1,-1%\logs\php_errors.log">>"%cd%\php_%osarc%%osold:~1,-1%\php.ini"
if not exist "%cd%\php_%osarc%%osold:~1,-1%\php_%phpexe:~1,-1%.exe" if exist "%cd%\php_%osarc%%osold:~1,-1%\php.exe" copy "%cd%\php_%osarc%%osold:~1,-1%\php.exe" "%cd%\php_%osarc%%osold:~1,-1%\php_%phpexe:~1,-1%.exe" /y
net.exe start "%servicename:~1,-1%"

exit 0