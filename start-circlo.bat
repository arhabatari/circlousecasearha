@echo off
setlocal

rem Circlo Laravel 13 one-click launcher
rem Put this file inside C:\laragon\www\circlo

set "PROJECT_DIR=%~dp0"
set "PHP_EXE=C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe"
set "NODE_DIR=C:\laragon\bin\nodejs\node-v22"

if /I "%~1"=="laravel" goto run_laravel
if /I "%~1"=="vite" goto run_vite

if not exist "%PROJECT_DIR%artisan" (
    echo [ERROR] artisan not found. Put start-circlo.bat in C:\laragon\www\circlo
    pause
    exit /b 1
)
if not exist "%PHP_EXE%" (
    echo [ERROR] PHP not found at: %PHP_EXE%
    echo Update PHP_EXE in this batch file to match your Laragon PHP folder.
    pause
    exit /b 1
)
if not exist "%NODE_DIR%\npm.cmd" (
    echo [ERROR] npm.cmd not found at: %NODE_DIR%\npm.cmd
    echo Update NODE_DIR in this batch file to match your Laragon Node.js folder.
    pause
    exit /b 1
)

rem Ensure PostgreSQL 18 is running before using database-backed pages.
sc query "postgresql-x64-18" 2>nul | findstr /C:"RUNNING" >nul
if errorlevel 1 (
    echo [NOTICE] PostgreSQL 18 service may be stopped or have a different service name.
    echo If Circlo shows a database error, open services.msc and start PostgreSQL 18.
    echo.
)

echo Starting Circlo Laravel and Vite...
start "Circlo - Laravel" "%ComSpec%" /k call "%~f0" laravel
start "Circlo - Vite" "%ComSpec%" /k call "%~f0" vite

echo Opening http://127.0.0.1:8000/
timeout /t 5 /nobreak >nul
start "" "http://127.0.0.1:8000/"
exit /b 0

:run_laravel
cd /d "%PROJECT_DIR%"
"%PHP_EXE%" artisan serve --host=127.0.0.1 --port=8000
exit /b %errorlevel%

:run_vite
cd /d "%PROJECT_DIR%"
set "PATH=%NODE_DIR%;%PATH%"
call "%NODE_DIR%\npm.cmd" run dev
exit /b %errorlevel%
