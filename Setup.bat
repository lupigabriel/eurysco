@echo off

setlocal enableextensions
cd /d "%~dp0"

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