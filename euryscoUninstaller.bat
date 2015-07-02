setlocal enableextensions
cd /d "%~dp0"

taskkill.exe /f /im euryscogui.exe /t

sc.exe stop euryscoCoreSSL
sc.exe stop euryscoExecutorSSL
sc.exe stop euryscoServerSSL
sc.exe stop euryscoCore
sc.exe stop euryscoExecutor
sc.exe stop euryscoServer
sc.exe stop euryscoAgent

taskkill.exe /f /im euryscosrv.exe /t
taskkill.exe /f /im httpd_eurysco_core.exe /t
taskkill.exe /f /im httpd_eurysco_executor.exe /t
taskkill.exe /f /im httpd_eurysco_server.exe /t
taskkill.exe /f /im php_eurysco_core.exe /t
taskkill.exe /f /im php_eurysco_executor.exe /t
taskkill.exe /f /im php_eurysco_server.exe /t
taskkill.exe /f /im php_eurysco_agent.exe /t
taskkill.exe /f /im eurysco.agent.status.check.exe /t
taskkill.exe /f /im eurysco.agent.exec.timeout.exe /t

wmic.exe os get osarchitecture | find "64"
if %errorlevel% equ 0 set osarc=x64
if %errorlevel% neq 0 set osarc=x86
ver.exe | find " 5."
if %errorlevel% equ 0 set osarc=x86&set osold="_xp2k3"
if %errorlevel% neq 0 set osold=""

netsh.exe firewall delete allowedprogram "%cd%\apache_%osarc%\bin\httpd_eurysco_core.exe" all
netsh.exe firewall delete allowedprogram "%cd%\apache_%osarc%\bin\httpd_eurysco_executor.exe" all
netsh.exe firewall delete allowedprogram "%cd%\apache_%osarc%\bin\httpd_eurysco_server.exe" all
netsh.exe advfirewall firewall delete rule name=httpd_eurysco_core dir=in
netsh.exe advfirewall firewall delete rule name=httpd_eurysco_executor dir=in
netsh.exe advfirewall firewall delete rule name=httpd_eurysco_server dir=in

del .\chromium\euryscoLogin.prt /f /q
del .\cert\*.* /f /q
del .\server\conf\*.xml /f /q
del .\agent\conf\*.xml /f /q
del .\agent\temp\*.status /f /q
del .\agent\conf\agent.init.php /f /q
del .\agent\temp\*.* /f /q
del .\agent\users\*.* /f /q
del .\core\conf\*.xml /f /q
del .\core\temp\*.* /f /q
del .\core\badaut\*.* /f /q
for /f "delims=" %%i in ('dir .\nodes /b') do rd .\nodes\%%i /s /q
del .\php_x86\php.ini /f /q
del .\php_x86_xp2k3\php.ini /f /q
del .\apache_x86\cert\eurysco.* /f /q
del .\apache_x86\logs\*.* /f /q
del .\apache_x86\tmp\*.* /f /q
type NUL >.\apache_x86\conf\httpd_eurysco_core_port.conf
type NUL >.\apache_x86\conf\httpd_eurysco_core_virtual.conf
type NUL >.\apache_x86\conf\httpd_eurysco_executor_port.conf
type NUL >.\apache_x86\conf\httpd_eurysco_executor_virtual.conf
type NUL >.\apache_x86\conf\httpd_eurysco_server_port.conf
type NUL >.\apache_x86\conf\httpd_eurysco_server_virtual.conf
type NUL >.\apache_x86\conf\httpd_srvroot.conf
del .\php_x64\php.ini /f /q
del .\apache_x64\cert\eurysco.* /f /q
del .\apache_x64\logs\*.* /f /q
del .\apache_x64\tmp\*.* /f /q
type NUL >.\apache_x64\conf\httpd_eurysco_core_port.conf
type NUL >.\apache_x64\conf\httpd_eurysco_core_virtual.conf
type NUL >.\apache_x64\conf\httpd_eurysco_executor_port.conf
type NUL >.\apache_x64\conf\httpd_eurysco_executor_virtual.conf
type NUL >.\apache_x64\conf\httpd_eurysco_server_port.conf
type NUL >.\apache_x64\conf\httpd_eurysco_server_virtual.conf
type NUL >.\apache_x64\conf\httpd_srvroot.conf

sc.exe delete euryscoAgent
sc.exe delete euryscoCore
sc.exe delete euryscoExecutor
sc.exe delete euryscoServer
sc.exe delete euryscoCoreSSL
sc.exe delete euryscoExecutorSSL
sc.exe delete euryscoServerSSL