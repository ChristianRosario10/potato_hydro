@echo off
echo Setting up Laravel Sensor Database...
echo.

set /p DB_USERNAME=Enter your MySQL username: 
set /p DB_PASSWORD=Enter your MySQL password: 

echo.
echo Creating database and importing schema...
mysql -u %DB_USERNAME% -p%DB_PASSWORD% -e "CREATE DATABASE IF NOT EXISTS laravel_sensor_db;"
mysql -u %DB_USERNAME% -p%DB_PASSWORD% laravel_sensor_db < create_sensor_database.sql

echo.
echo Updating .env file with database settings...
(
    echo DB_CONNECTION=mysql
    echo DB_HOST=127.0.0.1
    echo DB_PORT=3306
    echo DB_DATABASE=laravel_sensor_db
    echo DB_USERNAME=%DB_USERNAME%
    echo DB_PASSWORD=%DB_PASSWORD%
) > .env.db

type .env | findstr /v "DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD" > .env.temp
type .env.db >> .env.temp
move /y .env.temp .env
del .env.db

echo.
echo Database setup complete!
echo You can now run start_laravel.bat to start the server. 