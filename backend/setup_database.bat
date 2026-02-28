@echo off
echo Creating database and running migrations...
echo.

REM Create database
echo Creating database 'tutor'...
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS tutor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo.
echo Running migrations...
php artisan migrate:fresh

echo.
echo Seeding test questions...
php artisan db:seed --class=TestQuestionSeeder

echo.
echo Setup complete!
echo.
echo Now you can start the server with:
echo php artisan serve
pause
