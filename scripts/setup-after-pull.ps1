# WBPOS — run after every git pull (XAMPP or before Docker build)
# Usage:  powershell -ExecutionPolicy Bypass -File scripts/setup-after-pull.ps1

$ErrorActionPreference = "Stop"
Set-Location (Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path))

Write-Host "=== WBPOS setup after pull ===" -ForegroundColor Cyan

if (-not (Get-Command composer -ErrorAction SilentlyContinue)) {
    Write-Host "ERROR: Composer not found. Install from https://getcomposer.org/" -ForegroundColor Red
    exit 1
}

if (-not (Get-Command npm -ErrorAction SilentlyContinue)) {
    Write-Host "ERROR: Node.js/npm not found. Install from https://nodejs.org/" -ForegroundColor Red
    exit 1
}

Write-Host "`n[1/3] composer install..." -ForegroundColor Yellow
composer install
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "`n[2/3] npm install..." -ForegroundColor Yellow
npm install
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "`n[3/3] npm run build (CSS/JS assets)..." -ForegroundColor Yellow
npm run build
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

$resourceCount = (Get-ChildItem "public\resources" -Recurse -File -ErrorAction SilentlyContinue | Measure-Object).Count
if ($resourceCount -lt 10) {
    Write-Host "ERROR: public/resources looks empty ($resourceCount files). Build failed." -ForegroundColor Red
    exit 1
}

Write-Host "`nDone. Built $resourceCount files in public/resources/" -ForegroundColor Green
Write-Host "Hard-refresh browser with Ctrl+F5." -ForegroundColor Green
Write-Host ""
Write-Host "XAMPP URL:  http://localhost/opensourcepos/public/login" -ForegroundColor Cyan
Write-Host "Docker URL: http://localhost:8080/login" -ForegroundColor Cyan
