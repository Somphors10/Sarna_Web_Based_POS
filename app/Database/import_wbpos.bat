@echo off
setlocal
set MYSQL=c:\xampp8\mysql\bin\mysql.exe
set DB=wbpos
set DIR=%~dp0

echo ============================================================
echo WBPOS Fresh Database Import
echo Database: %DB%   Prefix: wbpos_
echo ============================================================
echo.

if not exist "%MYSQL%" (
    echo MySQL client not found at %MYSQL%
    echo Update MYSQL path in this script for your XAMPP install.
    pause
    exit /b 1
)

echo [1/3] Importing schema (wbpos.sql)...
"%MYSQL%" -u root < "%DIR%wbpos.sql"
if %ERRORLEVEL% NEQ 0 goto :failed

echo [2/3] Resetting clean tenant + admin (wbpos_clean_install.sql)...
"%MYSQL%" -u root %DB% < "%DIR%wbpos_clean_install.sql"
if %ERRORLEVEL% NEQ 0 goto :failed

echo [3/3] Loading demo data + role users (wbpos_demo_data.sql)...
"%MYSQL%" -u root %DB% < "%DIR%wbpos_demo_data.sql"
if %ERRORLEVEL% NEQ 0 goto :failed

echo.
echo Done! Update .env:
echo   database.default.database = wbpos
echo   database.default.DBPrefix = wbpos_
echo.
echo Demo logins (password: pointofsale):
echo   admin   - full access
echo   tom     - manager
echo   maria   - cashier
echo   nina    - sales lead
echo   paul    - stock
echo   rita    - accountant
echo.
echo Super Admin: superadmin / ChangeMe123!
echo.
pause
exit /b 0

:failed
echo.
echo Import failed. Check MySQL is running and credentials in this script.
pause
exit /b 1
