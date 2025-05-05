@echo off
echo Starting Laravel Sensor API application...
echo.

cd %~dp0
echo Installing dependencies...
call composer install --no-interaction

echo.
echo Generating application key...
call php artisan key:generate --no-interaction

echo.
echo Running database migrations...
call php artisan migrate --no-interaction

echo.
echo Starting Laravel server on 0.0.0.0:8000...
echo The server will be accessible at: http://192.168.79.146:8000
echo Press Ctrl+C to stop the server
echo.
call php artisan serve --host=0.0.0.0 --port=8000 