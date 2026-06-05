@echo off
echo Importing demo sample data into existing ospos database...
echo This does NOT delete your database. It only refreshes tenant 1 demo records.
echo.
c:\xampp8\mysql\bin\mysql.exe -u root ospos < "%~dp0demo_sample_data.sql"
if %ERRORLEVEL% EQU 0 (
    echo.
    echo Done! Login with admin / pointofsale and open Home dashboard.
) else (
    echo.
    echo Import failed. Check that MySQL is running and database ospos exists.
)
pause
