# Deploy to Linux (/var/www/html/attendance)

## Option 1: Clone from GitHub (Recommended)

```bash
cd /var/www/html
git clone https://github.com/Dane-22/attendance-v2.git attendance
cd attendance
```

## Option 2: Copy from Windows (WSL)

If using WSL, copy from Windows:

```bash
# In WSL terminal
sudo mkdir -p /var/www/html/attendance
sudo cp -r /mnt/c/wamp64/www/jajr-v2/* /var/www/html/attendance/
```

## Option 3: Manual File Transfer

1. Zip the project folder on Windows
2. Transfer to Linux via SCP, FTP, or file upload
3. Extract to `/var/www/html/attendance`

## Post-Setup Configuration

### 1. Set Permissions

```bash
cd /var/www/html/attendance
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 assets/images uploads/  # If writable dirs needed
```

### 2. Database Setup

```bash
# Import the SQL file
mysql -u root -p
CREATE DATABASE attendance_v2;
exit
mysql -u root -p attendance_v2 < attendance-system.sql
```

### 3. Environment Configuration

```bash
cp .env.example .env
nano .env  # Edit database credentials
```

Update `.env`:
```
DB_HOST=localhost
DB_NAME=attendance_v2
DB_USER=root
DB_PASS=your_password
```

### 4. Apache Configuration

Create virtual host `/etc/apache2/sites-available/attendance.conf`:

```apache
<VirtualHost *:80>
    ServerName attendance.local
    DocumentRoot /var/www/html/attendance

    <Directory /var/www/html/attendance>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/attendance_error.log
    CustomLog ${APACHE_LOG_DIR}/attendance_access.log combined
</VirtualHost>
```

Enable site:
```bash
sudo a2ensite attendance
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 5. Update Base URLs

Edit `index.php` and other files to update paths if needed:
- Change `/jajr-v2/` references to `/` or appropriate path

## Verify Installation

1. Visit `http://attendance.local` or `http://localhost/attendance`
2. Test the hamburger menu on mobile view
3. Verify database connection works

## Troubleshooting

- **403 Forbidden**: Check permissions (`www-data` ownership)
- **404 errors**: Enable mod_rewrite (`sudo a2enmod rewrite`)
- **Database errors**: Verify credentials in `.env` file
