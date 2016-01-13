setlocal enableextensions
cd /d "%~dp0"

wmic.exe os get osarchitecture | find "64"
if %errorlevel% equ 0 set osarc=x64
if %errorlevel% neq 0 set osarc=x86
ver.exe | find " 5."
if %errorlevel% equ 0 set osarc=x86&set osold="_xp2k3"
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

if %osarc% equ x86 if %osold% equ "_xp2k3" "%cd%\installs\vcredist_%osarc%%osold:~1,-1%.exe" /qb
if %osarc% equ x86 if %osold% equ "" "%cd%\installs\vcredist_%osarc%%osold:~1,-1%.exe" /passive /norestart
if %osarc% equ x64 "%cd%\installs\vcredist_%osarc%.exe" /passive /norestart

reg.exe delete "HKLM\SOFTWARE\eurysco" /f
type "%cd%\core\version.phtml" | find /i "return">"%cd%\version"
set /p version=<"%cd%\version"
del "%cd%\version" /f /q
reg.exe add "HKLM\SOFTWARE\eurysco" /v "DisplayName" /t REG_SZ /d "eurysco %version:~8,-2%" /f
reg.exe add "HKLM\SOFTWARE\eurysco" /v "DisplayVersion" /t REG_SZ /d "%version:~8,-2%" /f
reg.exe add "HKLM\SOFTWARE\eurysco" /v "HelpLink" /t REG_SZ /d "http://www.eurysco.com" /f
reg.exe add "HKLM\SOFTWARE\eurysco" /v "InstallLocation" /t REG_SZ /d "%cd%\\" /f
reg.exe add "HKLM\SOFTWARE\eurysco" /v "Publisher" /t REG_SZ /d "eurysco" /f
reg.exe add "HKLM\SOFTWARE\eurysco" /v "MajorVersion" /t REG_DWORD /d %version:~8,-5% /f
reg.exe add "HKLM\SOFTWARE\eurysco" /v "MinorVersion" /t REG_DWORD /d %version:~10,-2% /f
echo eurysco %version:~8,-2%>"%cd%\version.%version:~8,-2%"

cacls.exe "%cd%\chromium\data" /g "BUILTIN\Users":C /c /e

set servicename_last="eurysco"
set servicename="euryscoCore"
set servicestart="auto"
set serviceuser="LocalSystem"
set servicedisplay="eurysco Core"
if [%1] equ [] set serviceport="59980"
if [%1] neq [] set serviceport="%1"
if [%2] equ [] set phpport="59970"
if [%2] neq [] set phpport="%2"
set phpexe="eurysco_core"
set relpath="core"
set relname="core"

set sslprotocol=TLSv1.2
if %osold% equ "_xp2k3" set sslprotocol=TLSv1

echo ^<?xml version="1.0"?^>>.\conf\config_%relname:~1,-1%.xml
echo ^<config^>>>.\conf\config_%relname:~1,-1%.xml
echo 	^<settings^>>>.\conf\config_%relname:~1,-1%.xml
echo 		^<%relname:~1,-1%servicedisplayname^>%servicedisplay:~1,-1%^</%relname:~1,-1%servicedisplayname^>>>.\conf\config_%relname:~1,-1%.xml
echo 		^<%relname:~1,-1%servicename^>%servicename:~1,-1%^</%relname:~1,-1%servicename^>>>.\conf\config_%relname:~1,-1%.xml
echo 		^<%relname:~1,-1%servicestartuptype^>%servicestart:~1,-1%^</%relname:~1,-1%servicestartuptype^>>>.\conf\config_%relname:~1,-1%.xml
echo 		^<%relname:~1,-1%servicelogonas^>%serviceuser:~1,-1%^</%relname:~1,-1%servicelogonas^>>>.\conf\config_%relname:~1,-1%.xml
echo 		^<%relname:~1,-1%listeningport^>%serviceport:~1,-1%^</%relname:~1,-1%listeningport^>>>.\conf\config_%relname:~1,-1%.xml
echo 		^<%relname:~1,-1%phpport^>%phpport:~1,-1%^</%relname:~1,-1%phpport^>>>.\conf\config_%relname:~1,-1%.xml
echo 	^</settings^>>>.\conf\config_%relname:~1,-1%.xml
echo ^</config^>>>.\conf\config_%relname:~1,-1%.xml

if not exist "%cd%\cert\%phpexe:~1,-1%.crt" cd "%cd%\apache\bin" & openssl.exe req -x509 -nodes -days 1095 -newkey rsa:4096 -sha512 -keyout "..\..\cert\%phpexe:~1,-1%.key" -out "..\..\cert\%phpexe:~1,-1%.crt" -config "..\conf\openssl.cnf" -subj "/C=EU/ST=eurysco Any State/L=eurysco Any Locality/O=eurysco Any Organization/OU=eurysco/CN=%computername%" & openssl.exe req -out "..\..\cert\%phpexe:~1,-1%.csr" -key "..\..\cert\%phpexe:~1,-1%.key" -new -sha512 -config "..\conf\openssl.cnf" -subj "/C=EU/ST=eurysco Any State/L=eurysco Any Locality/O=eurysco Any Organization/OU=eurysco/CN=%computername%" & cd ..\..\

echo Define SRVROOT "%cd%\apache">"%cd%\apache\conf\httpd_srvroot.conf"
echo Listen %serviceport:~1,-1%>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_port.conf"
echo ^<VirtualHost *:%serviceport:~1,-1%^>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	SSLProxyEngine On>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	SSLEngine On>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	SSLProtocol %sslprotocol%>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	ProxyPreserveHost On>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	ProxyRequests Off>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	SSLCertificateFile      ../cert/%phpexe:~1,-1%.crt>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	SSLCertificateKeyFile   ../cert/%phpexe:~1,-1%.key>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	^<Location /^>>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 		ProxyPass http://127.0.0.1:%phpport:~1,-1%/ Timeout=180 KeepAlive=On>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 		ProxyPassReverse http://127.0.0.1:%phpport:~1,-1%/>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 		ProxyPreserveHost On>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 		SSLRequireSSL>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo 	^</Location^>>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
echo ^</VirtualHost^>>>"%cd%\apache\conf\httpd_%phpexe:~1,-1%_virtual.conf"
net.exe stop "%servicename_last:~1,-1%SSL"
taskkill.exe /f /im "httpd_%phpexe:~1,-1%.exe" /t
sc.exe delete "%servicename_last:~1,-1%SSL"
reg.exe delete "HKLM\SYSTEM\CurrentControlSet\services\%servicename_last:~1,-1%SSL" /f
netsh.exe firewall delete allowedprogram "%cd%\apache\bin\httpd_%phpexe:~1,-1%.exe" all
netsh.exe advfirewall firewall delete rule name=httpd_%phpexe:~1,-1% dir=in
sc.exe create "%servicename:~1,-1%SSL" start= "%servicestart:~1,-1%" binPath= "%cd%\euryscosrv.exe" obj= "%serviceuser:~1,-1%" DisplayName= "%servicedisplay:~1,-1% SSL"
reg.exe add "HKLM\SYSTEM\CurrentControlSet\services\%servicename:~1,-1%SSL\Parameters" /v "Application" /t REG_SZ /d "\"%cd%\apache\bin\httpd_%phpexe:~1,-1%.exe\" -f \"%cd%\apache\conf\httpd_%phpexe:~1,-1%.conf\"" /f
if %errorlevel% neq 0 cscript.exe "%cd%\euryscosrv.vbs" %servicename:~1,-1%SSL "@%cd%\apache\bin\httpd_%phpexe:~1,-1%.exe@ -f @%cd%\apache\conf\httpd_%phpexe:~1,-1%.conf@"
if not exist "%cd%\apache\bin\httpd_%phpexe:~1,-1%.exe" if exist "%cd%\apache\bin\httpd.exe" copy "%cd%\apache\bin\httpd.exe" "%cd%\apache\bin\httpd_%phpexe:~1,-1%.exe" /y
netsh.exe advfirewall firewall add rule name="httpd_%phpexe:~1,-1%" dir=in action=allow protocol=6 localport=%serviceport:~1,-1% program="%cd%\apache\bin\httpd_%phpexe:~1,-1%.exe" enable=yes
if %errorlevel% neq 0 netsh.exe firewall add allowedprogram "%cd%\apache\bin\httpd_%phpexe:~1,-1%.exe" "httpd_%phpexe:~1,-1%" enable

net.exe stop "%servicename_last:~1,-1%"
taskkill.exe /f /im "%phpexe:~1,-1%.exe" /t
sc.exe delete "%servicename_last:~1,-1%"
reg.exe delete "HKLM\SYSTEM\CurrentControlSet\services\%servicename_last:~1,-1%" /f

sc.exe create "%servicename:~1,-1%" start= "%servicestart:~1,-1%" binPath= "%cd%\euryscosrv.exe" obj= "%serviceuser:~1,-1%" DisplayName= "%servicedisplay:~1,-1%"
reg.exe add "HKLM\SYSTEM\CurrentControlSet\services\%servicename:~1,-1%\Parameters" /v "Application" /t REG_SZ /d "\"%cd%\php\php_%phpexe:~1,-1%.exe\" -c \"%cd%\php\php.ini\" -t \"%cd%\%relpath:~1,-1%\" -S 127.0.0.1:%phpport:~1,-1%" /f
if %errorlevel% neq 0 cscript.exe "%cd%\euryscosrv.vbs" %servicename:~1,-1% "@%cd%\php\php_%phpexe:~1,-1%.exe@ -c @%cd%\php\php.ini@ -t @%cd%\%relpath:~1,-1%@ -S 127.0.0.1:%phpport:~1,-1%"

if not exist "%cd%\php\php.ini" if exist "%cd%\php.default_%osarc%%osold:~1,-1%" copy "%cd%\php.default_%osarc%%osold:~1,-1%" "%cd%\php\php.ini" /y
type "%cd%\php\php.ini" | find /i "error_log = " | find /i "logs\php_errors.log"
if %errorlevel% neq 0 echo error_log = "%cd%\php\logs\php_errors.log">>"%cd%\php\php.ini"
type "%cd%\php\php.ini" | find /i "upload_tmp_dir = " | find /i "temp"
if %errorlevel% neq 0 echo upload_tmp_dir = "%cd%\php\temp">>"%cd%\php\php.ini"
if not exist "%cd%\php\php_%phpexe:~1,-1%.exe" if exist "%cd%\php\php.exe" copy "%cd%\php\php.exe" "%cd%\php\php_%phpexe:~1,-1%.exe" /y

echo %serviceport:~1,-1%>"%cd%\chromium\euryscoLogin.prt

echo. |cacls.exe "%cd%" /s:D:PAI(D;OICI;DCLCRPDTCRSDWDWO;;;SY)(A;OICI;0x1200a9;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)>"%cd%\cacls"
set /p cacls=<"%cd%\cacls"
del "%cd%\cacls" /f /q
echo %cacls:~-5,-4%|cacls.exe "%cd%" /s:D:PAI(D;OICI;DCLCRPDTCRSDWDWO;;;SY)(A;OICI;0x1200a9;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\agent\conf" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\agent\groups" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\agent\temp" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\agent\users" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\apache\conf" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\apache\logs" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\apache\tmp" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\audit" /s:D:PAI(D;OICI;DTSDWDWO;;;SY)(A;OICI;0x1201bf;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\badaut\core" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\badaut\server" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\cert" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\chromium" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\conf" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\core\php-firewall\logs.txt" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\groups" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\nodes" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\php\logs" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\php\temp" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\metering" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\server\php-firewall\logs.txt" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\server\settings" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\sqlite" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\temp" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)
echo %cacls:~-5,-4%|cacls.exe "%cd%\users" /s:D:PAI(A;OICI;0x1301ff;;;SY)(A;OICI;FA;;;BA)(A;OICI;0x1200a9;;;BU)

net.exe start "%servicename:~1,-1%"
net.exe start "%servicename:~1,-1%SSL"

exit 0