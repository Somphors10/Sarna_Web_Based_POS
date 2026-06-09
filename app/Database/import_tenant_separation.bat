@echo off
echo Importing full tenant separation migration...
c:\xampp8\mysql\bin\mysql.exe -u root ospos < "%~dp0Migrations\sqlscripts\2026_06_05_tenant_full_separation.sql"
if %ERRORLEVEL% EQU 0 (
    echo Done. Clear writable\cache\settings if dashboard shows old data.
) else (
    echo Import failed. Check MySQL credentials.
)
pause
