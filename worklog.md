# Branch QR Attendance System - Work Log

**Date:** April 14, 2026  
**Project:** JAJR Attendance System v2  
**Feature:** Branch-specific QR Attendance

---

## Summary

Implemented a complete branch-specific QR attendance system that allows devices at different construction sites to log in and record employee attendance with automatic branch tagging.

---

## Features Implemented

### 1. Database Schema Updates
- **SQL Script:** `database/migrations/add_branch_to_attendance.sql` (Schema Update)
  - Added `branch_code` column to `attendance` table
  - Added index for faster branch-based queries

- **SQL Script:** `database/migrations/add_branch_to_admins.sql` (Schema Update)
  - Added `branch_code` column to `admins` table for branch device assignment
  - Added index for branch lookups

- **Seed Data:** `database/migrations/create_branch_credentials.sql` (Data Insert)
  - Created 6 branch device accounts (branch-a through branch-f)
  - Password: `branch123` for all accounts
  - Branch codes: A-F mapped to locations (Sto. Rosario, BCDA, Sundara, Panicsican, Main Office, Capitol)

### 2. Model Updates
- **File:** `models/Attendance.php`
  - Updated `getAllWithEmployees()` - Added branch_name join
  - Updated `getByEmployeeId()` - Added branch_name join
  - Updated `getByDateRange()` - Added branch_name join
  - Updated `getByDate()` - Added branch_name join
  - Updated `create()` - Added branch_code field
  - Added `getTodayByEmployeeAndBranch()` - Check attendance by branch
  - Added `getLastTodayByEmployee()` - Get latest attendance record
  - Added `updateCheckOut()` - Record check-out time

### 3. Controllers Created/Updated

#### BranchQRController (`controllers/BranchQRController.php`)
- `scanner()` - Renders QR scanner interface for logged-in branch devices
- `processScan()` - Handles QR code scan, validates employee, records check-in/out
- `login()` - Branch device login with branch selection
- `logout()` - Destroys session, redirects to home

#### LoginController (`controllers/LoginController.php`)
- Updated `login()` - Auto-redirects branch-assigned admins to QR scanner
- Updated `logout()` - Redirects to home page instead of login
- Added `branchLogin()` - Dedicated branch device login endpoint

### 4. Views Created

#### Branch QR Scanner (`views/branch_qr/scanner.php`)
- Full-screen camera interface with QR scanning
- Animated scan overlay with golden frame
- Check-in/Check-out status feedback
- Real-time scan result display
- Offline mode indicator
- Branch badge showing current location
- 3-second cooldown between scans
- Auto-logout on inactivity

#### Scanner Layout (`views/layouts/scanner.php`)
- PWA-ready layout for mobile devices
- Service worker registration
- Mobile-optimized viewport

#### Branch Login (`views/auth/branch-login.php`)
- Dark theme login form
- Branch dropdown selector
- Username/password fields
- Link back to admin login

#### Attendance List Update (`views/attendance/all.php`)
- Added "Branch" column to attendance table
- Shows branch code badge and name
- Branch filter integration

### 5. Routes Added (`app.php`)
```php
// Branch QR Scanner routes
$router->add('branch/scanner', ['controller' => 'BranchQRController', 'action' => 'scanner']);
$router->add('branch/scan', ['controller' => 'BranchQRController', 'action' => 'processScan']);
$router->add('branch/logout', ['controller' => 'BranchQRController', 'action' => 'logout']);
```

---

## QR Code Format

Employee QR codes follow this format:
```
JAJR-EMP:ID|EMPLOYEE_CODE|NAME
```

Example:
```
JAJR-EMP:1|JAJR-001|John Doe
```

---

## Check-in/Check-out Logic

### Flow
1. **First scan at Branch A** → Records check-in at Branch A
2. **Second scan at Branch A** → Records check-out at Branch A
3. **Scan at Branch B without checkout from A** → Error: "Must check out from A first"
4. **After checkout at A, scan at B** → Records check-in at Branch B

### Cross-Branch Validation
- System tracks last attendance record per employee
- If employee at different branch without checkout, blocks new check-in
- Multiple check-in/out cycles allowed per day at same branch

---

## Credentials

| Branch | Username | Password | Location |
|--------|----------|----------|----------|
| A | branch-a | branch123 | Sto. Rosario |
| B | branch-b | branch123 | BCDA |
| C | branch-c | branch123 | Sundara |
| D | branch-d | branch123 | Panicsican |
| E | branch-e | branch123 | Main Office |
| F | branch-f | branch123 | Capitol |

---

## URLs

| Endpoint | Description |
|----------|-------------|
| `/jajr-v2/login` | Main login (auto-redirects branch devices to scanner) |
| `/jajr-v2/branch/scanner` | QR scanner interface |
| `/jajr-v2/branch/scan` | AJAX endpoint for QR processing |
| `/jajr-v2/branch/logout` | Logout and redirect to home |
| `/jajr-v2/attendance/all` | Attendance records with branch column |
| `/jajr-v2/branches` | Branch management |

---

## Technical Stack

- **Frontend:** HTML5, CSS3, JavaScript, jsQR library
- **Backend:** PHP 8.x, MVC Architecture
- **Database:** MySQL with branch_code indexes
- **Camera:** getUserMedia API for QR scanning
- **PWA:** Service worker ready for offline support

---

## Testing Instructions

1. **Setup:**
   ```sql
   -- Run these SQL files in phpMyAdmin (in order):
   -- 1. add_branch_to_attendance.sql (adds column)
   -- 2. add_branch_to_admins.sql (adds column)
   -- 3. create_branch_credentials.sql (inserts accounts)
   ```

2. **Login:**
   - Go to `http://localhost/jajr-v2/login`
   - Use `branch-a` / `branch123`
   - Auto-redirects to QR scanner

3. **Scanning:**
   - Allow camera access
   - Point at employee QR code
   - See check-in/out feedback

4. **Logout:**
   - Click logout button
   - Redirects to `http://localhost/jajr-v2/`

---

## Known Issues & Solutions

| Issue | Solution |
|-------|----------|
| "Unknown column 'status'" | Removed status from INSERT statement |
| "Duplicate entry for email" | Added unique email addresses per branch |
| "Password mismatch" | Used correct password_hash() output |
| "User not found" | Ensure SQL accounts were created in database |

---

## Files Modified/Created

### New Files
- `controllers/BranchQRController.php`
- `views/branch_qr/scanner.php`
- `views/layouts/scanner.php`
- `views/auth/branch-login.php`
- `database/migrations/add_branch_to_attendance.sql` (SQL Script)
- `database/migrations/add_branch_to_admins.sql` (SQL Script)
- `database/migrations/create_branch_credentials.sql` (Seed Data)

### Modified Files
- `models/Attendance.php`
- `controllers/LoginController.php`
- `views/attendance/all.php`
- `app.php`

---

## Docker & Deployment (April 16, 2026)

### Docker Setup for Local Development

**Files Created:**
- `Dockerfile` - PHP 8.2 Apache with required extensions
- `docker-compose.yml` - App, MySQL 8.0, phpMyAdmin services
- `.dockerignore` - Excludes local files from image
- `env.docker` - Environment variables template
- `docker/apache/000-default.conf` - Apache mod_rewrite config
- `docker/php/php.ini` - PHP configuration
- `DOCKER_README.md` - Local development setup instructions

**Database Connection Updated:**
- `conn/db_connection.php` - Uses `getenv()` with fallbacks
- `config/database.php` - Environment-based configuration

### Production Deployment

**Server Details:**
- IP: `72.62.254.60`
- Domain: `attendance.xandree.com`
- SSL: Let's Encrypt certificate
- Web Server: Nginx → PHP-FPM

**Deployment Steps:**
```bash
git push origin main  # Local
git pull origin main  # Server (72.62.254.60)
```

### DNS Configuration

**Hostinger DNS Records:**
- Type: A Record
- Name: `attendance.xandree.com`
- Points to: `72.62.254.60`
- TTL: 14400 seconds (4 hours)

**Propagation Status:**
- Server (localhost): ✅ Resolves immediately
- Global DNS (8.8.8.8, 1.1.1.1): ⏳ Waiting for propagation
- Full propagation: 4-48 hours

### QR Scanner HTTPS Fix

**Issue:** QR scanner showed "network error check connection" when accessing via IP

**Root Cause:** HTTPS redirect blocked HTTP access on IP addresses during DNS propagation

**Fix Applied:** `views/branch_qr/scanner.php`
- Modified HTTPS redirect to allow IP-based HTTP access
- Added regex check: `/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/`
- Scanner now works at `http://72.62.254.60/branch/scanner`

**Working URLs:**
| URL | Status |
|-----|--------|
| `https://attendance.xandree.com` | ✅ (after DNS) |
| `http://72.62.254.60` | ✅ QR scanner accessible |
| API `/branch/scan` | ✅ Responding correctly |

---

## Dashboard Redesign & Enhancement (In Progress - April 16, 2026)

### Overview
Redesigning the admin dashboard at `/jajr-v2/dashboard` with a modern dark theme, analytics charts, and improved data visualization.

### Features Being Implemented

#### 1. DashboardController Updates (`controllers/DashboardController.php`)
- Added `getSevenDayTrend()` - 7-day attendance trend data
- Added `getBranchAttendanceStats()` - Branch-wise attendance counts
- Added `getPositionDistribution()` - Employee distribution by position
- Added `getRecentTransfers()` - Recent employee transfer records
- Added `getSystemActivity()` - System activity log feed

#### 2. Model Updates (`models/Attendance.php`)
- Added `countByBranchAndDate()` - Count attendance records by branch and date

#### 3. New Dashboard Features
- **Dark Theme UI** - Modern dark color scheme with accent colors
- **Analytics Cards** - Total Employees, Active Branches, Attendance Rate, Today's Status
- **7-Day Attendance Chart** - Trend visualization for present/late/absent
- **Overtime Chart** - Bar chart for overtime hours by day
- **Branch Attendance Chart** - Bar chart showing attendance per branch
- **Employee Distribution** - Pie/donut chart by position
- **Quick Action Command Center** - Action buttons for common tasks
- **Recent Transfers Section** - Employee transfer history
- **System Activity Feed** - Real-time activity log

### Files Being Modified
- `controllers/DashboardController.php` - New stats methods
- `models/Attendance.php` - `countByBranchAndDate()` method
- `views/dashboard/index.php` - Complete redesign with charts

---

## Bug Fixes (April 16, 2026 - Part 2)

### QR Scanner Camera Access Fix

**Issue:** `https://attendance.xandree.com/branch/scanner` showed "Camera access denied" even with permissions granted

**Root Cause:**
- Page served behind proxy was detecting HTTP instead of HTTPS
- `navigator.mediaDevices` was undefined (requires secure context)
- Mobile browsers have strict camera constraints

**Fix Applied:** `views/branch_qr/scanner.php`
1. Added HTTPS redirect at page load for non-localhost
2. Improved camera initialization with fallback constraints (environment camera → any camera)
3. Added detailed error diagnostics showing specific error names

### Attendance Page "Failed to fetch" Fix

**Issue:** `https://attendance.xandree.com/attendance` showed "Error loading employees: Failed to fetch"

**Root Cause:**
- Page loaded via HTTPS but API calls used relative paths resolving to HTTP
- Mixed content blocked by browser security

**Fix Applied:** `views/attendance/site_attendance.php`
1. Added HTTPS redirect check at script start
2. Changed `basePath` from relative PHP path to `window.location.origin`

### Time Out Action Debugging

**Issue:** "Time Out" button on attendance page not working

**Investigation:** Added console logging to `markAttendance()` function to diagnose the issue

---

## Hostinger VPS Deployment (April 16, 2026)

### Deployment Summary
Successfully deployed JAJR Attendance System to Hostinger VPS using Git repository workflow.

### Server Configuration
- **VPS Provider:** Hostinger
- **IP Address:** `72.62.254.60`
- **OS:** Ubuntu 24.04.4 LTS
- **Web Server:** Nginx (port 80)
- **PHP:** PHP 8.3-FPM
- **Database:** MariaDB 10.11.14
- **Deployment Path:** `/var/www/html/attendance`

### Deployment Steps Completed

#### 1. Server Setup
```bash
# Update system and install dependencies
apt-get update
apt-get install -y git nginx php8.3 php8.3-fpm php8.3-mysql mariadb-server mariadb-client

# Start and enable services
systemctl start nginx php8.3-fpm mariadb
systemctl enable nginx php8.3-fpm mariadb
```

#### 2. Git Repository Deployment
```bash
cd /var/www/html
git clone https://github.com/Dane-22/attendance-v2.git attendance
cd attendance
git pull origin main  # For future updates
```

#### 3. Database Setup
- Created database: `attendance_v2`
- Created user: `jajr_user` with password `SecurePass123!`
- Imported schema from `attendance-system.sql`
- Granted all privileges on `attendance_v2.*`

#### 4. Database Configuration Updates

**`config/database.php`:**
```php
<?php
return [
    'host' => 'localhost',
    'database' => 'attendance_v2',
    'username' => 'jajr_user',
    'password' => 'SecurePass123!',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci'
];
```

**`conn/db_connection.php`:**
```php
<?php
$host = 'localhost';
$dbname = 'attendance_v2';
$username = 'jajr_user';
$password = 'SecurePass123!';
// ... PDO and mysqli connections
```

#### 5. Nginx Configuration
```nginx
server {
    listen 80;
    server_name 72.62.254.60;
    root /var/www/html/attendance;
    index index.php app.php;

    # Redirect /jajr-v2/ paths to root
    location ~ ^/jajr-v2/(.*)$ {
        return 301 /$1;
    }

    location / {
        try_files $uri $uri/ /app.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### Database Tables
| Table | Description |
|-------|-------------|
| `admins` | Admin and branch device accounts |
| `attendance` | Attendance records with branch_code |
| `branch_users` | Branch user mappings |
| `branches` | Branch locations (A-F) |
| `employees` | Employee records with QR codes |

### Working URLs
| Endpoint | URL | Status |
|----------|-----|--------|
| Homepage | `http://72.62.254.60` | Live |
| Login | `http://72.62.254.60/login` | Working |
| QR Scanner | `http://72.62.254.60/branch/scanner` | Accessible |
| Admin Dashboard | `http://72.62.254.60/dashboard` | Functional |

### Future Domain Setup (Planned)
- Configure custom domain (TBD)
- Install SSL certificate (Let's Encrypt)
- Update Nginx server_name directive
- Configure DNS A record to 72.62.254.60

---

## Next Steps (Future Enhancements)

- [ ] Add offline sync with IndexedDB
- [ ] Implement service worker for PWA
- [ ] Add sound feedback for scans
- [ ] Create attendance reports by branch
- [ ] Add branch-wise employee assignment
- [ ] Implement shift management per branch

---

