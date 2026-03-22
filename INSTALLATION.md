# የንብረት ጥያቄ አስተዳደር ስርዓት - የመጫኛ መመሪያ
# Property Request Management System - Installation Guide

## Quick Start Guide

### Step 1: Database Setup

1. Make sure MySQL is running
2. Run the initialization script:

```bash
php database/init.php
```

This will:
- Create the `property_request_system` database
- Create all 7 tables with UTF-8mb4 encoding for Amharic support
- Create 6 sample users (one for each department)

### Step 2: Configure Database Connection

Edit `config/database.php` if your MySQL credentials are different:

```php
return [
    'host' => 'localhost',
    'dbname' => 'property_request_system',
    'username' => 'root',      // Change if needed
    'password' => '',          // Change if needed
    'charset' => 'utf8mb4',
];
```

### Step 3: Web Server Setup

#### Option A: PHP Built-in Server (For Testing)

```bash
cd public
php -S localhost:8000
```

Then open: http://localhost:8000

#### Option B: Apache

1. Point your document root to the `public` directory
2. Create `.htaccess` in `public` directory:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

3. Make sure `mod_rewrite` is enabled

#### Option C: Nginx

Add to your server block:

```nginx
root /path/to/property-request-system/public;
index index.php;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}
```

### Step 4: Test the System

1. Open your browser and go to the application URL
2. You'll be redirected to the login page
3. Use one of the sample credentials:

| Department | Username | Password |
|------------|----------|----------|
| Requester | requester1 | password123 |
| Requester Main Dept | requester_main1 | password123 |
| Property Mgmt Main Dept | property_main1 | password123 |
| Property Mgmt Dept | property_dept1 | password123 |
| Registry Office | registry1 | password123 |
| Treasury | treasury1 | password123 |

## Testing the Workflow

### Complete Workflow Test:

1. **Login as Requester** (requester1/password123)
   - Click "አዲስ ጥያቄ ፍጠር" (Create New Request)
   - Fill Form 20 with item details
   - Submit

2. **Login as Requester Main Dept** (requester_main1/password123)
   - See the pending request
   - Click "ፍቀድ" (Approve)

3. **Login as Property Mgmt Main Dept** (property_main1/password123)
   - See the pending request
   - Click "ፍቀድ" (Approve)

4. **Login as Property Mgmt Dept** (property_dept1/password123)
   - See the pending request
   - Click "ፍቀድ" (Approve)
   - Fill item registration form
   - Submit

5. **Login as Registry Office** (registry1/password123)
   - See the pending request
   - Click "ፍቀድ" (Approve)
   - System automatically creates backup

6. **Login as Treasury** (treasury1/password123)
   - See the pending request
   - Verify item registration details
   - Mark as retrieved, then release to requester

7. **Login back as Requester** (requester1/password123)
   - See request status as "ተጠናቋል" (Completed)

## Troubleshooting

### Database Connection Error

- Check MySQL is running: `mysql -u root -p`
- Verify credentials in `config/database.php`
- Ensure database exists: `SHOW DATABASES;`

### Permission Errors

```bash
chmod -R 755 public
chmod -R 777 logs  # Create logs directory if needed
```

### Amharic Text Not Displaying

- Ensure your browser supports UTF-8
- Check that database tables use `utf8mb4_unicode_ci` collation
- Verify web server sends correct charset header

### Session Issues

- Check PHP session directory is writable
- Verify `session.save_path` in php.ini
- Clear browser cookies and try again

## System Requirements

- PHP 8.1 or higher
- MySQL 8.0 or higher
- Web server (Apache/Nginx) or PHP built-in server
- Modern web browser with UTF-8 support

## Security Notes

- Change default passwords in production
- Use HTTPS in production
- Set proper file permissions
- Configure firewall rules
- Regular database backups

## Next Steps

- Add more users through database
- Customize Amharic translations in `lang/am.php`
- Implement additional features from tasks.md
- Add property-based tests
- Set up automated backups

## Support

For issues or questions, refer to:
- README.md for system overview
- tasks.md for remaining implementation tasks
- Database schema in database/schema.sql
