# Setting Up Laravel Potato Hydroponics on InfinityFree

This guide provides step-by-step instructions to deploy your Laravel Hydroponics Monitoring System on InfinityFree hosting.

## 1. Prepare Your Laravel Application

### 1.1 Generate Optimized Files
```bash
# In your local development environment
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 1.2 Modify .env for Production
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-infinityfree-domain.com
```

## 2. Set Up InfinityFree Account

1. Sign up at [InfinityFree](https://infinityfree.net/)
2. Activate your account
3. Create a new hosting account with your desired subdomain (e.g., potato-hydroponics.epizy.com)
4. Create a new MySQL database in the InfinityFree control panel
5. Note down the database credentials

## 3. File Structure on InfinityFree

InfinityFree requires a specific file structure. Your setup should look like:

```
htdocs/ (public_html/ on some servers)
    ├── index.php (modified to point to Laravel)
    ├── .htaccess
    ├── assets/ (copied from Laravel's public folder)
    └── other public files...
laravel/ (folder outside public_html)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── resources/
    ├── routes/
    ├── vendor/
    └── other Laravel folders...
```

## 4. Upload Project Files

1. Connect to your InfinityFree account via FTP:
   - Host: ftpupload.net
   - Username: epiz_[your-username]
   - Password: [your-password]
   - Port: 21

2. Create a `laravel` folder in the root directory (outside public_html)
3. Upload your entire Laravel project to this folder
4. Copy all files from your Laravel's `public` folder to the `htdocs` (or `public_html`) folder

## 5. Configure Public Access

1. Create/modify the `index.php` file in the public_html folder:

```php
<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 */

// Point to the Laravel installation outside public_html
define('LARAVEL_START', microtime(true));

// Modify these paths to point to your Laravel installation
require __DIR__.'/../laravel/vendor/autoload.php';

$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

// Run the application
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
```

2. Create a `.htaccess` file in the public_html folder:

```
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

## 6. Configure Database Connection

1. Edit the `.env` file in the `laravel` folder with your InfinityFree database credentials:

```
DB_CONNECTION=mysql
DB_HOST=sql304.infinityfree.com  # Use your actual database host
DB_PORT=3306
DB_DATABASE=epiz_your_database
DB_USERNAME=epiz_your_username
DB_PASSWORD=your_password
```

## 7. Testing the Setup

1. Run the migration on your local environment:
   ```bash
   php artisan migrate --seed
   ```

2. Copy the database to InfinityFree using phpMyAdmin (accessible from the InfinityFree control panel)

3. Visit your InfinityFree domain to check if the application is working correctly

## 8. Troubleshooting Common Issues

### File Permissions
If you encounter errors, ensure the storage and bootstrap/cache directories have write permissions:

```bash
chmod -R 775 laravel/storage
chmod -R 775 laravel/bootstrap/cache
```

### Database Connection Issues
Double-check your database credentials and make sure the database host is correct.

### 500 Internal Server Error
Check the .htaccess file and make sure it's correctly formatted.

### Shared URLs Not Working
Ensure the URL rewriting is correctly set up in your .htaccess file.

## 9. Updating Your Application on InfinityFree

When you need to update your application:

1. Make changes locally
2. Test thoroughly
3. Build/optimize the application
4. Upload the changed files to your InfinityFree hosting

Remember to clear the cache if needed:
```
php artisan cache:clear
```

## 10. Using the Shareable URLs Feature

The shareable URL feature will work automatically once the application is correctly set up on InfinityFree. To share your sensor readings:

1. Go to the Share Dashboard tab
2. Select the sensors you want to include
3. Click "Generate Share Link"
4. Copy the link and share it with others

Recipients of the shared link will see a simplified dashboard with real-time updates of your sensor data. 