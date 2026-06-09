@echo off
echo Backfilling tenant config and reference data...
c:\xampp8\mysql\bin\mysql.exe -u root ospos < "%~dp0Migrations\sqlscripts\2026_06_09_backfill_existing_tenants.sql"
if %ERRORLEVEL% NEQ 0 (
    echo SQL backfill failed.
    pause
    exit /b 1
)
echo Done. Delete writable\cache\settings* and log in again.
pause
