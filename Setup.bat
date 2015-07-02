@echo off

setlocal enableextensions
cd /d "%~dp0"

md ".\cert"
md ".\nodes"
md ".\agent\conf"
md ".\agent\groups"
md ".\agent\temp"
md ".\agent\users"
md ".\apache_x64\logs"
md ".\apache_x64\manual"
md ".\apache_x64\tmp"
md ".\apache_x64\conf\ssl"
md ".\apache_x86\logs"
md ".\apache_x86\manual"
md ".\apache_x86\tmp"
md ".\apache_x86\conf\ssl"
md ".\chromium\Data"
md ".\core\audit"
md ".\core\autosuggest"
md ".\core\badaut"
md ".\core\conf"
md ".\core\groups"
md ".\core\js"
md ".\core\temp"
md ".\php_x64\extras"
md ".\php_x64\logs"
md ".\php_x86\extras"
md ".\php_x86\logs"
md ".\php_x86_xp2k3\logs"
md ".\server\audit"
md ".\server\badaut"
md ".\server\conf"
md ".\server\groups"
md ".\server\metering"
md ".\server\settings"
md ".\server\users"

cls
set /p CorePhpPort=Core PHP Local Port: 
set /p CorePort=Core Listening SSL Port: 
start "euryscoCoreInstaller" /wait euryscoCoreInstaller.bat %CorePort% %CorePhpPort%

cls
set /p ExecutorPhpPort=Executor PHP Local Port: 
set /p ExecutorPort=Executor Listening SSL Port: 
start "euryscoExecutorInstaller" /wait euryscoExecutorInstaller.bat %ExecutorPort% %ExecutorPhpPort%

cls
set /p ServerAddress=Server Connection Address: 
set /p ServerPort=Server Connection Port: 
set /p ServerPassword=Server Connection Password: 
start "euryscoAgentInstaller" /wait euryscoAgentInstaller.bat %ServerPort% %ServerAddress% %ServerPassword%

cls
echo eurysco Setup Completed...
echo.