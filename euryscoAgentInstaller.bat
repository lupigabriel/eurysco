setlocal enableextensions
cd /d "%~dp0"

wmic.exe os get osarchitecture | find "64"
if %errorlevel% equ 0 set osarc=x64
if %errorlevel% neq 0 set osarc=x86
ver.exe | find " 5."
if %errorlevel% equ 0 set osarc=x86&set osold="_xp2k3"
if %errorlevel% neq 0 set osold=""

set servicename_last="eurysco"
set servicename="euryscoAgent"
set servicestart="auto"
set serviceuser="LocalSystem"
set servicedisplay="eurysco Agent"
if [%1] equ [] exit 0
if [%1] neq [] set serverport="%1"
if [%2] equ [] exit 0
if [%2] neq [] set serveraddress="%2"
if [%3] equ [] set serverpassword="''"
if [%3] neq [] set serverpassword="'%3'"
if [%4] neq [] exit 0
set phpexe="eurysco_agent"
set relpath="core"
set relname="agent"

if %serveraddress:~1,8% neq https:// if %serveraddress:~1,7% neq http:// set serveraddress="https://%serveraddress:~1,-1%"
if %serveraddress:~1,7% equ http:// set serveraddress="https://%serveraddress:~8,-1%"

type "%cd%\agent\agent.inittop">"%cd%\agent\conf\agent.install.php"
echo $agentpath = '%cd%\agent';>>"%cd%\agent\conf\agent.install.php"
echo $agentservicedisplayname_xml = '%servicedisplay:~1,-1%';>>"%cd%\agent\conf\agent.install.php"
echo $agentservicename_xml = '%servicename:~1,-1%';>>"%cd%\agent\conf\agent.install.php"
echo $agentservicestartuptype_xml = '%servicestart:~1,-1%';>>"%cd%\agent\conf\agent.install.php"
echo $agentservicelogonas_xml = '%serviceuser:~1,-1%';>>"%cd%\agent\conf\agent.install.php"
echo $serverconnectionaddress_xml = '%serveraddress:~1,-1%';>>"%cd%\agent\conf\agent.install.php"
echo $serverconnectionport_xml = '%serverport:~1,-1%';>>"%cd%\agent\conf\agent.install.php"
echo $serverconnectionpassword = %serverpassword:~1,-1%;>>"%cd%\agent\conf\agent.install.php"
type "%cd%\agent\agent.install">>"%cd%\agent\conf\agent.install.php"
type "%cd%\agent\agent.initbot">>"%cd%\agent\conf\agent.install.php"
"%cd%\php_%osarc%%osold:~1,-1%\php.exe" -c "%cd%\php_%osarc%%osold:~1,-1%\php.ini" "%cd%\agent\conf\agent.install.php"

copy "%cd%\agent\conf\config_agent.xml" "%cd%\%relpath:~1,-1%\conf\config_agent.xml" /y
copy "%cd%\%relpath:~1,-1%\conf\config_core.xml" "%cd%\agent\conf\config_core.xml" /y
copy "%cd%\%relpath:~1,-1%\conf\config_executor.xml" "%cd%\agent\conf\config_executor.xml" /y
copy "%cd%\%relpath:~1,-1%\conf\config_settings.xml" "%cd%\agent\conf\config_settings.xml" /y
del "%cd%\agent\temp\agent.status" /f /q
type "%cd%\agent\agent.inittop">"%cd%\agent\conf\agent.init.php"
echo session_save_path('%cd%\agent\temp'); session_start(); $_SESSION['agentpath'] = '%cd%\agent'; include($_SESSION['agentpath'] . '\\' . 'agent.php'); session_write_close();>>"%cd%\agent\conf\agent.init.php"
type "%cd%\agent\agent.initbot">>"%cd%\agent\conf\agent.init.php"

net.exe stop "%servicename_last:~1,-1%"
taskkill.exe /f /im "%phpexe:~1,-1%.exe" /t
sc.exe delete "%servicename_last:~1,-1%"
reg.exe delete "HKLM\SYSTEM\CurrentControlSet\services\%servicename_last:~1,-1%" /f
rem netsh.exe firewall delete allowedprogram "%cd%\php_%osarc%%osold:~1,-1%\php_%phpexe:~1,-1%.exe" all
rem netsh.exe advfirewall firewall delete rule name=php_%phpexe:~1,-1% dir=in

sc.exe create "%servicename:~1,-1%" start= "%servicestart:~1,-1%" binPath= "%cd%\euryscosrv.exe" obj= "%serviceuser:~1,-1%" DisplayName= "%servicedisplay:~1,-1%"
reg.exe add "HKLM\SYSTEM\CurrentControlSet\services\%servicename:~1,-1%\Parameters" /v "Application" /t REG_SZ /d "\"%cd%\php_%osarc%%osold:~1,-1%\php_%phpexe:~1,-1%.exe\" -c \"%cd%\php_%osarc%%osold:~1,-1%\php.ini\" \"%cd%\agent\conf\agent.init.php\"" /f

if not exist "%cd%\php_%osarc%%osold:~1,-1%\php.ini" if exist "%cd%\php.default_%osarc%%osold:~1,-1%" copy "%cd%\php.default_%osarc%%osold:~1,-1%" "%cd%\php_%osarc%%osold:~1,-1%\php.ini" /y
type "%cd%\php_%osarc%%osold:~1,-1%\php.ini" | find /i "error_log = " | find /i "logs\php_errors.log"
if %errorlevel% neq 0 echo error_log = "%cd%\php_%osarc%%osold:~1,-1%\logs\php_errors.log">>"%cd%\php_%osarc%%osold:~1,-1%\php.ini"
if not exist "%cd%\php_%osarc%%osold:~1,-1%\php_%phpexe:~1,-1%.exe" if exist "%cd%\php_%osarc%%osold:~1,-1%\php.exe" copy "%cd%\php_%osarc%%osold:~1,-1%\php.exe" "%cd%\php_%osarc%%osold:~1,-1%\php_%phpexe:~1,-1%.exe" /y
rem netsh.exe advfirewall firewall add rule name="php_%phpexe:~1,-1%" dir=in action=allow program="%cd%\php_%osarc%%osold:~1,-1%\php_%phpexe:~1,-1%.exe" enable=yes
rem if %errorlevel% neq 0 netsh.exe firewall add allowedprogram "%cd%\php_%osarc%%osold:~1,-1%\php_%phpexe:~1,-1%.exe" "php_%phpexe:~1,-1%" enable
net.exe start "%servicename:~1,-1%"

exit 0