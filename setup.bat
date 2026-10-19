@echo off
REM ==========================================================
REM  Zanis TV - first-time local setup (Windows / XAMPP)
REM  Run from the project folder:  setup.bat
REM  Before running: start Apache + MySQL in the XAMPP Control
REM  Panel and create an empty database named "zanis_tv".
REM ==========================================================
setlocal
cd /d "%~dp0"

where php >nul 2>nul || (echo [ERROR] php not found. Add C:\xampp\php to your PATH. & exit /b 1)
where composer >nul 2>nul || (echo [ERROR] composer not found. Install from https://getcomposer.org & exit /b 1)
where npm >nul 2>nul || (echo [ERROR] npm not found. Install Node.js LTS from https://nodejs.org & exit /b 1)

if not exist .env (
    copy .env.example .env >nul
    php artisan key:generate
)

echo.
echo === 1/6 Installing PHP packages (composer install) ===
call composer install || exit /b 1

echo.
echo === 2/6 Installing JS packages (npm install) ===
call npm install || exit /b 1

echo.
echo === 3/6 Building CSS/JS (npm run build) ===
call npm run build || exit /b 1

echo.
echo === 4/6 Creating tables (php artisan migrate) ===
php artisan migrate --force || (echo [ERROR] Migration failed. Is MySQL running and does the "zanis_tv" database exist? & exit /b 1)

echo.
echo === 5/6 Seeding roles, admin user and categories ===
php artisan db:seed --force || exit /b 1

echo.
echo === 6/6 Linking public storage ===
if not exist public\storage php artisan storage:link

php artisan optimize:clear

echo.
echo ==========================================================
echo  Done! Now open TWO terminals in this folder and run:
echo     php artisan serve
echo     php artisan queue:work --timeout=3600
echo  Then visit  http://127.0.0.1:8000
echo  Admin login: http://127.0.0.1:8000/login  (see ADMIN_EMAIL / ADMIN_PASSWORD in .env)
echo ==========================================================
endlocal
