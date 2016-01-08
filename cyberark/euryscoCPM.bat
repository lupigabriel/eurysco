setlocal enableextensions

if [%1] equ [] exit 2104
if [%2] equ [] exit 2104

set euryscopath=### eurysco installation absolute path ###
set usertype=### user type ###
set userauth=### user authentication ###
set nextexppwddays=### password expiration days ###
set reconcileuser=### user for reconciliation ###
set userini=%1
set action=%2
set rnd=%random%%random%%random%
type %userini% | find /i "username">"%SystemRoot%\Temp\%rnd%"
if %errorlevel% neq 0 exit 2104
set /p usernm=<"%SystemRoot%\Temp\%rnd%"
del "%SystemRoot%\Temp\%rnd%" /f /q

if not exist "%euryscopath%\php\php_eurysco_agent.exe" exit 2104
if not exist "%euryscopath%\php\php.ini" exit 2104
if not exist "%euryscopath%\cyberark\cyberark.php" exit 2104

"%euryscopath%\php\php_eurysco_agent.exe" -c "%euryscopath%\php_%osarc%\php.ini" "%euryscopath%\cyberark\cyberark.php"

exit %errorlevel%