@echo off
setlocal enabledelayedexpansion

:: Ensure script always executes from its own directory regardless of how it was launched
cd /d "%~dp0"

title Valenti Atelier - SEN 803 B2C E-Commerce Portal Launcher
color 0F

echo ================================================================
echo           VALENTI ATELIER - B2C LUXURY CLOTHING PORTAL
echo           SEN 803 Software Technology Course Project
echo ================================================================
echo.
echo  [✓] Working Directory: %CD%
echo  [✓] Checking PHP Runtime Environment...

set PHP_BIN=php
if exist "C:\xampp\php\php.exe" (
    set PHP_BIN="C:\xampp\php\php.exe"
)

%PHP_BIN% -v >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo [!] ERROR: PHP was not found in C:\xampp\php or system PATH.
    echo Please ensure XAMPP is installed or PHP is added to PATH.
    pause
    exit /b 1
)

:: Free port 8000 if a previous instance is still holding it
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8000 ^| findstr LISTENING 2^>nul') do (
    echo  [*] Reclaiming port 8000 from stale process PID %%a...
    taskkill /F /PID %%a >nul 2>&1
)

echo  [✓] Running Database Auto-Setup & Asset Verification...
%PHP_BIN% "%~dp0database\seed.php"

echo.
echo ================================================================
echo  SERVER READY!
echo  Storefront URL:      http://localhost:8000/
echo  Admin Portal:        http://localhost:8000/admin/index.php
echo.
echo  DEMO CREDENTIALS:
echo  - Admin Access:      admin@valenti.com  /  admin123
echo  - Patron Access:     customer@valenti.com  /  customer123
echo  - Discount Voucher:  SEN803 (20%% Academic Discount)
echo ================================================================
echo.
echo Launching http://localhost:8000/ in your default browser...
timeout /t 2 >nul
start http://localhost:8000/

echo Press Ctrl+C to terminate the local server when finished.
echo.
%PHP_BIN% -S localhost:8000 -t "%~dp0"
pause


