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

echo  [✓] Running Database Auto-Setup & Asset Verification...
%PHP_BIN% "%~dp0database\seed.php"

echo.
echo ================================================================
echo  SERVER READY!
echo  Storefront URL:      http://localhost:8000
echo  Admin Portal:        http://localhost:8000/admin/index.php
echo.
echo  DEMO CREDENTIALS:
echo  - Admin Access:      admin@valenti.com  /  admin123
echo  - Patron Access:     customer@valenti.com  /  customer123
echo  - Discount Voucher:  SEN803 (20%% Academic Discount)
echo ================================================================
echo.
echo Launching your default browser in 2 seconds...
timeout /t 2 >nul
start http://localhost:8000/index.php

echo Press Ctrl+C to terminate the local server when finished.
echo.
%PHP_BIN% -S localhost:8000 -t "%~dp0"
pause

