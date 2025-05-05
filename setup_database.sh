#!/bin/bash
echo "Setting up Laravel Sensor Database..."
echo

# Change to the script's directory
cd "$(dirname "$0")"

read -p "Enter your MySQL username: " DB_USERNAME
read -s -p "Enter your MySQL password: " DB_PASSWORD
echo

echo
echo "Creating database and importing schema..."
mysql -u $DB_USERNAME -p$DB_PASSWORD -e "CREATE DATABASE IF NOT EXISTS laravel_sensor_db;"
mysql -u $DB_USERNAME -p$DB_PASSWORD laravel_sensor_db < create_sensor_database.sql

echo
echo "Updating .env file with database settings..."
# Create temporary .env file with database settings
cat > .env.db << EOL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_sensor_db
DB_USERNAME=$DB_USERNAME
DB_PASSWORD=$DB_PASSWORD
EOL

# Merge the files
grep -v "DB_CONNECTION\|DB_HOST\|DB_PORT\|DB_DATABASE\|DB_USERNAME\|DB_PASSWORD" .env > .env.temp
cat .env.db >> .env.temp
mv .env.temp .env
rm .env.db

echo
echo "Database setup complete!"
echo "You can now run ./start_laravel.sh to start the server."

# Make the start script executable
chmod +x start_laravel.sh 