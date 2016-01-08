setlocal enableextensions
cd /d "%~dp0"

wmic.exe os get osarchitecture | find "64"
if %errorlevel% equ 0 set osarc=x64
if %errorlevel% neq 0 set osarc=x86
ver.exe | find " 5."
if %errorlevel% equ 0 set osarc=x86&set osold="_xp2k3"
if %errorlevel% neq 0 set osold=""

set clr=0
if exist "%cd%\php_%osarc%%osold:~1,-1%" if not exist "%cd%\php" ren "%cd%\php_%osarc%%osold:~1,-1%" "php" & set clr=1
if exist "%cd%\php_x64" rd "%cd%\php_x64" /s /q
if exist "%cd%\php_x86" rd "%cd%\php_x86" /s /q
if exist "%cd%\php_x86_xp2k3" rd "%cd%\php_x86_xp2k3" /s /q
if %clr% equ 1 cscript.exe "euryscoclr.vbs"

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
"%cd%\php\php.exe" -c "%cd%\php\php.ini" "%cd%\agent\conf\agent.install.php"

copy "%cd%\agent\conf\config_agent.xml" "%cd%\conf\config_agent.xml" /y
del "%cd%\agent\conf\config_agent.xml" /f /q
del "%cd%\agent\temp\agent.status" /f /q
type "%cd%\agent\agent.inittop">"%cd%\agent\conf\agent.init.php"
echo session_save_path('%cd%\agent\temp'); session_start(); $_SESSION['agentpath'] = '%cd%\agent'; include($_SESSION['agentpath'] . '\\' . 'agent.php'); session_write_close();>>"%cd%\agent\conf\agent.init.php"
type "%cd%\agent\agent.initbot">>"%cd%\agent\conf\agent.init.php"

net.exe stop "%servicename_last:~1,-1%"
taskkill.exe /f /im "%phpexe:~1,-1%.exe" /t
sc.exe delete "%servicename_last:~1,-1%"
reg.exe delete "HKLM\SYSTEM\CurrentControlSet\services\%servicename_last:~1,-1%" /f

sc.exe create "%servicename:~1,-1%" start= "%servicestart:~1,-1%" binPath= "%cd%\euryscosrv.exe" obj= "%serviceuser:~1,-1%" DisplayName= "%servicedisplay:~1,-1%"
reg.exe add "HKLM\SYSTEM\CurrentControlSet\services\%servicename:~1,-1%\Parameters" /v "Application" /t REG_SZ /d "\"%cd%\php\php_%phpexe:~1,-1%.exe\" -c \"%cd%\php\php.ini\" \"%cd%\agent\conf\agent.init.php\"" /f
if %errorlevel% neq 0 cscript.exe "%cd%\euryscosrv.vbs" %servicename:~1,-1% "@%cd%\php\php_%phpexe:~1,-1%.exe@ -c @%cd%\php\php.ini@ @%cd%\agent\conf\agent.init.php@"

if not exist "%cd%\php\php.ini" if exist "%cd%\php.default_%osarc%%osold:~1,-1%" copy "%cd%\php.default_%osarc%%osold:~1,-1%" "%cd%\php\php.ini" /y
type "%cd%\php\php.ini" | find /i "error_log = " | find /i "logs\php_errors.log"
if %errorlevel% neq 0 echo error_log = "%cd%\php\logs\php_errors.log">>"%cd%\php\php.ini"
type "%cd%\php\php.ini" | find /i "upload_tmp_dir = " | find /i "temp"
if %errorlevel% neq 0 echo upload_tmp_dir = "%cd%\php\temp">>"%cd%\php\php.ini"
if not exist "%cd%\php\php_%phpexe:~1,-1%.exe" if exist "%cd%\php\php.exe" copy "%cd%\php\php.exe" "%cd%\php\php_%phpexe:~1,-1%.exe" /y

net.exe start "%servicename:~1,-1%"

exit 0