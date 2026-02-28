@echo off
echo Clearing Laravel cache...
php artisan config:clear
php artisan cache:clear
php artisan route:clear

echo.
echo Cache cleared! Now restart the server with:
echo php artisan serve
