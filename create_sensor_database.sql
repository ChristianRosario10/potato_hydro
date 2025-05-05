-- Create the database
CREATE DATABASE IF NOT EXISTS laravel_sensor_db;
USE laravel_sensor_db;

-- Create failed_jobs table
CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` varchar(255) NOT NULL,
    `connection` text NOT NULL,
    `queue` text NOT NULL,
    `payload` longtext NOT NULL,
    `exception` longtext NOT NULL,
    `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create migrations table
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` varchar(255) NOT NULL,
    `batch` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create password_resets table
CREATE TABLE IF NOT EXISTS `password_resets` (
    `email` varchar(255) NOT NULL,
    `token` varchar(255) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create personal_access_tokens table
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tokenable_type` varchar(255) NOT NULL,
    `tokenable_id` bigint(20) UNSIGNED NOT NULL,
    `name` varchar(255) NOT NULL,
    `token` varchar(64) NOT NULL,
    `abilities` text DEFAULT NULL,
    `last_used_at` timestamp NULL DEFAULT NULL,
    `expires_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
    KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create main sensor_data table
CREATE TABLE IF NOT EXISTS `sensor_data` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `temperature` double(8,2) DEFAULT NULL,
    `humidity` double(8,2) DEFAULT NULL,
    `soil_moisture` double(8,2) DEFAULT NULL,
    `motion_detected` tinyint(1) NOT NULL DEFAULT 0,
    `relay_state` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `sensor_data_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `email_verified_at` timestamp NULL DEFAULT NULL,
    `password` varchar(255) NOT NULL,
    `remember_token` varchar(100) DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add some useful views for sensor data analysis
CREATE OR REPLACE VIEW `daily_sensor_averages` AS
SELECT 
    DATE(created_at) as date,
    AVG(temperature) as avg_temperature,
    AVG(humidity) as avg_humidity,
    AVG(soil_moisture) as avg_soil_moisture,
    COUNT(*) as reading_count
FROM sensor_data
GROUP BY DATE(created_at);

-- Add triggers for data validation
DELIMITER //
CREATE TRIGGER before_sensor_data_insert 
BEFORE INSERT ON sensor_data
FOR EACH ROW
BEGIN
    -- Ensure temperature is within reasonable range (-50 to 100)
    IF NEW.temperature < -50 OR NEW.temperature > 100 THEN
        SET NEW.temperature = NULL;
    END IF;
    
    -- Ensure humidity is between 0 and 100
    IF NEW.humidity < 0 OR NEW.humidity > 100 THEN
        SET NEW.humidity = NULL;
    END IF;
    
    -- Ensure soil_moisture is between 0 and 100
    IF NEW.soil_moisture < 0 OR NEW.soil_moisture > 100 THEN
        SET NEW.soil_moisture = NULL;
    END IF;
    
    -- Set timestamps if not provided
    IF NEW.created_at IS NULL THEN
        SET NEW.created_at = NOW();
    END IF;
    IF NEW.updated_at IS NULL THEN
        SET NEW.updated_at = NOW();
    END IF;
END;//
DELIMITER ;

-- Create stored procedures for common queries
DELIMITER //
CREATE PROCEDURE GetLatestSensorData()
BEGIN
    SELECT * FROM sensor_data
    ORDER BY created_at DESC
    LIMIT 1;
END;//

CREATE PROCEDURE GetSensorDataByDateRange(
    IN start_date DATETIME,
    IN end_date DATETIME
)
BEGIN
    SELECT * FROM sensor_data
    WHERE created_at BETWEEN start_date AND end_date
    ORDER BY created_at;
END;//
DELIMITER ; 