@echo off
echo ========================================
echo Cai dat thu vien QR Code va PDF
echo ========================================
echo.

REM Kiem tra PHP
if not exist "C:\xampp\php\php.exe" (
    echo LOI: Khong tim thay PHP tai C:\xampp\php\php.exe
    echo Vui long kiem tra duong dan XAMPP cua ban.
    pause
    exit /b 1
)

echo [1/3] Dang tai Composer...
C:\xampp\php\php.exe -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

echo [2/3] Dang cai dat Composer...
C:\xampp\php\php.exe composer-setup.php

echo [3/3] Dang cai dat thu vien QR Code va PDF...
C:\xampp\php\php.exe composer.phar install

echo.
echo ========================================
echo Hoan thanh!
echo ========================================
echo.
echo Thu vien da duoc cai dat. Ban co the xoa file composer-setup.php neu muon.
echo.
pause

