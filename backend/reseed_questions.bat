@echo off
echo Reseeding test questions...
echo.

echo Clearing old questions...
php artisan db:seed --class=TestQuestionSeeder

echo.
echo Done! Now you have:
echo - 10 Mathematics questions
echo - 10 Physics questions
echo - 10 Chemistry questions
echo - 10 English questions
echo - 10 Personality questions
echo.
echo Total: 50 questions
echo.
pause
