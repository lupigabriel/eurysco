setlocal enableextensions
cd /d "%~dp0"

wmic.exe os get osarchitecture | find "64"
if %errorlevel% equ 0 set osarc=x64
if %errorlevel% neq 0 set osarc=x86
ver.exe | find " 5."
if %errorlevel% equ 0 set osarc=x86&set osold="_xp2k3"&set sslprotocol=TLSv1
if %errorlevel% neq 0 set osold=""

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

if %relpath:~1,-1% neq agent if not exist "%cd%\cert\%phpexe:~1,-1%.crt" cd "%cd%\apache_%osarc%\bin" & openssl.exe req -x509 -nodes -days 1825 -newkey rsa:2048 -sha512 -keyout "..\..\cert\%phpexe:~1,-1%.key" -out "..\..\cert\%phpexe:~1,-1%.crt" -config "..\conf\openssl.cnf" -subj "/C=EU/ST=eurysco Any State/L=eurysco Any Locality/O=eurysco Any Organization/OU=eurysco/CN=%computername%" & openssl.exe req -new -key "..\..\cert\%phpexe:~1,-1%.key" -out "..\..\cert\%phpexe:~1,-1%.csr" -config "..\conf\openssl.cnf" -subj "/C=EU/ST=eurysco Any State/L=eurysco Any Locality/O=eurysco Any Organization/OU=eurysco/CN=%computername%" & cd ..\..\
if %relpath:~1,-1% neq agent echo Define SRVROOT "%cd%\apache_%osarc%">"%cd%\apache_%osarc%\conf\httpd_srvroot.conf"
if %relpath:~1,-1% neq agent echo Listen %serviceport:~1,-1%>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_port.conf"
if %relpath:~1,-1% neq agent echo ^<VirtualHost *:%serviceport:~1,-1%^>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	SSLProxyEngine On>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	SSLEngine On>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	SSLProtocol %sslprotocol%>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	ProxyPreserveHost On>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	ProxyRequests Off>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	SSLCertificateFile      ../cert/%phpexe:~1,-1%.crt>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	SSLCertificateKeyFile   ../cert/%phpexe:~1,-1%.key>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	^<Location /^>>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 		ProxyPass http://127.0.0.1:%phpport:~1,-1%/ Timeout=300 KeepAlive=On>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 		ProxyPassReverse http://127.0.0.1:%phpport:~1,-1%/>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 		ProxyPreserveHost On>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 		SSLRequireSSL>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo 	^</Location^>>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent echo ^</VirtualHost^>>>"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%_virtual.conf"
if %relpath:~1,-1% neq agent net.exe stop "%servicename_last:~1,-1%SSL"
if %relpath:~1,-1% neq agent taskkill.exe /f /im "httpd_%phpexe:~1,-1%.exe" /t
if %relpath:~1,-1% neq agent sc.exe delete "%servicename_last:~1,-1%SSL"
if %relpath:~1,-1% neq agent reg.exe delete "HKLM\SYSTEM\CurrentControlSet\services\%servicename_last:~1,-1%SSL" /f
if %relpath:~1,-1% neq agent netsh.exe firewall delete allowedprogram "%cd%\apache_%osarc%\bin\httpd_%phpexe:~1,-1%.exe" all
if %relpath:~1,-1% neq agent netsh.exe advfirewall firewall delete rule name=httpd_%phpexe:~1,-1% dir=in
if %relpath:~1,-1% neq agent sc.exe create "%servicename:~1,-1%SSL" start= "%servicestart:~1,-1%" binPath= "%cd%\euryscosrv.exe" obj= "%serviceuser:~1,-1%" DisplayName= "%servicedisplay:~1,-1% SSL"
if %relpath:~1,-1% neq agent reg.exe add "HKLM\SYSTEM\CurrentControlSet\services\%servicename:~1,-1%SSL\Parameters" /v "Application" /t REG_SZ /d "\"%cd%\apache_%osarc%\bin\httpd_%phpexe:~1,-1%.exe\" -f \"%cd%\apache_%osarc%\conf\httpd_%phpexe:~1,-1%.conf\"" /f
if %relpath:~1,-1% neq agent if not exist "%cd%\apache_%osarc%\bin\httpd_%phpexe:~1,-1%.exe" if exist "%cd%\apache_%osarc%\bin\httpd.exe" copy "%cd%\apache_%osarc%\bin\httpd.exe" "%cd%\apache_%osarc%\bin\httpd_%phpexe:~1,-1%.exe" /y
if %relpath:~1,-1% neq agent netsh.exe advfirewall firewall add rule name="httpd_%phpexe:~1,-1%" dir=in action=allow protocol=6 localport=%serviceport:~1,-1% program="%cd%\apache_%osarc%\bin\httpd_%phpexe:~1,-1%.exe" enable=yes
if %relpath:~1,-1% neq agent if %errorlevel% neq 0 netsh.exe firewall add allowedprogram "%cd%\apache_%osarc%\bin\httpd_%phpexe:~1,-1%.exe" "httpd_%phpexe:~1,-1%" enable
if %relpath:~1,-1% neq agent net.exe start "%servicename:~1,-1%SSL"

net.exe stop "%servicename_last:~1,-1%"
taskkill.exe /f /im "php_%phpexe:~1,-1%.exe" /t
if %relpath:~1,-1% equ agent taskkill.exe /f /im "eurysco.agent.status.check.exe" /im "eurysco.agent.exec.timeout.exe" /t
sc.exe delete "%servicename_last:~1,-1%"
reg.exe delete "HKLM\SYSTEM\CurrentControlSet\services\%servicename_last:~1,-1%" /f

sc.exe create "%servicename:~1,-1%" start= "%servicestart:~1,-1%" binPath= "%cd%\euryscosrv.exe" obj= "%serviceuser:~1,-1%" DisplayName= "%servicedisplay:~1,-1%"
if %relpath:~1,-1% neq agent reg.exe add "HKLM\SYSTEM\CurrentControlSet\services\%servicename:~1,-1%\Parameters" /v "Application" /t REG_SZ /d "\"%cd%\php_%osarc%%osold:~1,-1%\php_%phpexe:~1,-1%.exe\" -c \"%cd%\php_%osarc%%osold:~1,-1%\php.ini\" -t \"%cd%\%relpath:~1,-1%\" -S 127.0.0.1:%phpport:~1,-1%" /f
if %relpath:~1,-1% equ agent reg.exe add "HKLM\SYSTEM\CurrentControlSet\services\%servicename:~1,-1%\Parameters" /v "Application" /t REG_SZ /d "\"%cd%\php_%osarc%%osold:~1,-1%\php_%phpexe:~1,-1%.exe\" -c \"%cd%\php_%osarc%%osold:~1,-1%\php.ini\" \"%cd%\agent\conf\agent.init.php\"" /f

if %relpath:~1,-1% equ agent copy "%cd%\core\conf\config_agent.xml" "%cd%\agent\conf\config_agent.xml" /y
if %relpath:~1,-1% equ agent copy "%cd%\core\conf\config_settings.xml" "%cd%\agent\conf\config_settings.xml" /y
if %relpath:~1,-1% equ agent copy "%cd%\core\conf\config_core.xml" "%cd%\agent\conf\config_core.xml" /y
if %relpath:~1,-1% equ agent copy "%cd%\core\conf\config_executor.xml" "%cd%\agent\conf\config_executor.xml" /y
if %relpath:~1,-1% equ agent del "%cd%\agent\temp\agent.status" /f /q
if %relpath:~1,-1% equ server copy "%cd%\core\conf\config_server.xml" "%cd%\server\conf\config_server.xml" /y
if %relpath:~1,-1% equ server copy "%cd%\core\conf\config_settings.xml" "%cd%\server\conf\config_settings.xml" /y
if %relpath:~1,-1% equ server copy "%cd%\core\conf\config_core.xml" "%cd%\server\conf\config_core.xml" /y
if %relpath:~1,-1% equ server copy "%cd%\core\conf\config_executor.xml" "%cd%\server\conf\config_executor.xml" /y
if %relpath:~1,-1% equ server copy "%cd%\euryscoServer.default" "%cd%\sqlite\euryscoServer" /y
if %relpath:~1,-1% equ server if not exist "%cd%\sqlite\euryscoAudit" copy "%cd%\euryscoAudit.default" "%cd%\sqlite\euryscoAudit" /y

if %relpath:~1,-1% equ agent type "%cd%\agent\agent.inittop">"%cd%\agent\conf\agent.init.php"
if %relpath:~1,-1% equ agent echo session_save_path('%cd%\agent\temp'); session_start(); $_SESSION['agentpath'] = '%cd%\agent'; include($_SESSION['agentpath'] . '\\' . 'agent.php'); session_write_close();>>"%cd%\agent\conf\agent.init.php"
if %relpath:~1,-1% equ agent type "%cd%\agent\agent.initbot">>"%cd%\agent\conf\agent.init.php"

if not exist "%cd%\php_%osarc%%osold:~1,-1%\php.ini" if exist "%cd%\php.default_%osarc%%osold:~1,-1%" copy "%cd%\php.default_%osarc%%osold:~1,-1%" "%cd%\php_%osarc%%osold:~1,-1%\php.ini" /y
type "%cd%\php_%osarc%%osold:~1,-1%\php.ini" | find /i "error_log = " | find /i "logs\php_errors.log"
if %errorlevel% neq 0 echo error_log = "%cd%\php_%osarc%%osold:~1,-1%\logs\php_errors.log">>"%cd%\php_%osarc%%osold:~1,-1%\php.ini"
if not exist "%cd%\php_%osarc%%osold:~1,-1%\php_%phpexe:~1,-1%.exe" if exist "%cd%\php_%osarc%%osold:~1,-1%\php.exe" copy "%cd%\php_%osarc%%osold:~1,-1%\php.exe" "%cd%\php_%osarc%%osold:~1,-1%\php_%phpexe:~1,-1%.exe" /y
net.exe start "%servicename:~1,-1%"