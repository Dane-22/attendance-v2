# Server Administrator's Deployment Manual

Use this procedure to update jajr.xandree.com whenever new features or fixes are pushed to the repository.

## 1. The "Daily" Code Update

When your colleague says "Code is updated on GitHub," run this sequence:

```bash
# 1. Enter the project directory
cd /var/www/html/attendance

# 2. Force the server to match GitHub exactly
# This discards local changes and pulls the latest main branch
git fetch origin main
git reset --hard origin/main

# 3. Fix Ownership
# Ensures Apache/Nginx and PHP have permission to read the new files
sudo chown -R www-data:www-data /var/www/html/attendance
sudo chmod -R 755 /var/www/html/attendance

# 4. Restart PHP to clear any cached scripts
sudo systemctl restart php8.2-fpm
# OR for Apache mod_php:
sudo systemctl restart apache2
```

## 2. The Database Update

If the update includes new database tables or columns (usually sent as a .sql file):

1. **Upload the file** to your server (or find it in the project folder if it was pushed to Git).

2. **Import the data** using this command:
   ```bash
   mysql -u root -p attendance_v2 < /var/www/html/attendance/attendance-system.sql
   ```

3. **Verify**: Log in to MySQL to ensure the new tables are there:
   ```bash
   mysql -u root -p -e "USE attendance_v2; SHOW TABLES;"
   ```

## 3. Environment Persistence (.env)

Your .env file is the most important file on the server. Because it is ignored by Git, it will not be deleted when you run `git pull` or `git reset`.

If you ever need to change your database password, edit this file only:

```bash
nano /var/www/html/attendance/.env
```

## 4. Quick-Reference Table

| Task | Command | Why? |
|------|---------|------|
| Check for Errors | `sudo tail -f /var/log/apache2/error.log` | To see why a page is showing a "500 Error." |
| Test Apache Config | `sudo apache2ctl configtest` | To ensure your site settings are valid. |
| Check .env | `ls -la /var/www/html/attendance/.env` | To confirm the password file is still present. |
| Monitor PHP | `sudo systemctl status php8.2-fpm` | To ensure the PHP engine is running. |
| Check Git Status | `cd /var/www/html/attendance && git status` | To see if local files differ from GitHub. |

## 5. Summary "One-Liner"

For experienced users, you can run all update steps in a single string:

```bash
cd /var/www/html/attendance && git fetch origin main && git reset --hard origin/main && sudo chown -R www-data:www-data /var/www/html/attendance && sudo chmod -R 755 /var/www/html/attendance && sudo systemctl restart apache2
```

---

## First-Time Setup (if not yet cloned)

If the project doesn't exist on the server yet:

```bash
cd /var/www/html
git clone https://github.com/Dane-22/attendance-v2.git attendance
cd attendance
cp .env.example .env
nano .env  # Add your database credentials
sudo chown -R www-data:www-data /var/www/html/attendance
```
