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

## Employee Database Import & TablePlus Setup (April 16, 2026 - Part 3)

### Summary
Successfully imported 86 employees from source SQL file and configured employee codes for all Worker positions with standardized W#### format.

### Files Created
- `database/migrations/import_employees.sql` - Migration script for employee import

### Database Schema Extension
**Added columns to `employees` table:**
- `middle_name` VARCHAR(100) - Employee middle name
- `status` VARCHAR(50) - Active/Inactive status
- `profile_image` VARCHAR(255) - Profile photo path
- `daily_rate` DECIMAL(10,2) - Salary computation
- `performance_allowance` DECIMAL(10,2) - Additional compensation
- `has_deduction` TINYINT(1) - Deduction flag
- `branch_id` INT(11) - Branch assignment

**Extended column lengths:**
- `employee_code` VARCHAR(50)
- `first_name` VARCHAR(100)
- `last_name` VARCHAR(100)

### Data Import Details

**Source:** `employees.sql` (86 employee records)

**Field Mapping:**
| Source Field | Target Field | Notes |
|--------------|--------------|-------|
| employee_code | employee_code | Imported as-is |
| first_name | first_name | Direct copy |
| middle_name | middle_name | NULL if empty |
| last_name | last_name | Direct copy |
| email | email | Direct copy |
| position | position | Direct copy |
| status | status | Active/Inactive |
| profile_image | profile_image | Path preserved |
| daily_rate | daily_rate | Decimal value |
| performance_allowance | performance_allowance | Decimal value |
| has_deduction | has_deduction | 0 or 1 |
| branch_id | branch_id | Foreign key |
| N/A | department | Derived from position |

**Department Derivation Logic:**
- Worker → Operations
- Engineer → Engineering
- Admin → Administration
- Developer → IT
- Super Admin → Administration

### Employee Code Standardization

**Worker Position Update:**
- All 87 Worker positions updated to W#### format
- Sequential numbering: W0001, W0002, W0003... W0087
- Used ROW_NUMBER() window function for reliable sequential assignment

**Other Positions:**
- Engineers: ENG-2026-#### (7 employees)
- Admins: ADMIN-2026-#### (5 employees)
- Developers: IT-2026-## (2 employees)
- Super Admin: SA001 (1 employee)

### TablePlus Connection Setup

**Connection Details:**
```
Host: 127.0.0.1
Port: 3306
User: attendance_user
Password: JaJr12390786@
Database: attendance-system
```

**Features Enabled:**
- Visual data browsing and editing
- SQL query execution
- Schema modification capabilities
- Import/export functionality

### SQL Queries Used

**Column Extension:**
```sql
ALTER TABLE employees 
  MODIFY employee_code VARCHAR(50),
  MODIFY first_name VARCHAR(100),
  MODIFY last_name VARCHAR(100),
  MODIFY email VARCHAR(100);

ALTER TABLE employees ADD COLUMN middle_name VARCHAR(100) NULL AFTER last_name;
ALTER TABLE employees ADD COLUMN status VARCHAR(50) DEFAULT 'Active' AFTER position;
ALTER TABLE employees ADD COLUMN profile_image VARCHAR(255) NULL;
ALTER TABLE employees ADD COLUMN daily_rate DECIMAL(10,2) DEFAULT 600.00;
ALTER TABLE employees ADD COLUMN performance_allowance DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE employees ADD COLUMN has_deduction TINYINT(1) DEFAULT 1;
ALTER TABLE employees ADD COLUMN branch_id INT(11) NULL;
```

**Worker Code Update:**
```sql
UPDATE employees e1
JOIN (
    SELECT id, ROW_NUMBER() OVER (ORDER BY id) as row_num
    FROM employees
    WHERE position = 'Worker'
) e2 ON e1.id = e2.id
SET e1.employee_code = CONCAT('W', LPAD(e2.row_num, 4, '0'))
WHERE e1.position = 'Worker';
```

### Current Employee Count
| Position | Count |
|----------|-------|
| Worker | 87 |
| Engineer | 7 |
| Admin | 5 |
| Developer | 2 |
| Super Admin | 1 |
| **Total** | **102** |

---

## Next Steps (Future Enhancements)

- [ ] Add offline sync with IndexedDB
- [ ] Implement service worker for PWA
- [ ] Add sound feedback for scans
- [ ] Create attendance reports by branch
- [ ] Add branch-wise employee assignment
- [ ] Implement shift management per branch

---

## Attendance Interface Enhancement & JWT Authentication Fix (April 16, 2026)

### Summary
Enhanced the attendance interface at `/attendance` with improved button logic and fixed JWT authentication issues causing 401 Unauthorized errors.

### Attendance Button Logic Update
**File:** `views/attendance/site_attendance.php`

**Changes:**
- **No attendance record** → Show "Mark Absent" (red) + "Time In" (green)
- **Checked in only** → Show "Time Out" button only
- **Checked out** → Show "Time In" button only (allows new check-in)

**Implementation:**
```javascript
${!emp.check_in && !emp.check_out ?
    `<button class="btn-pill btn-absent" onclick="markAttendance(${emp.id}, 'absent')">Mark Absent</button>
     <button class="btn-pill btn-timein" onclick="markAttendance(${emp.id}, 'present')">Time In</button>` :
    !emp.check_out ?
        `<button class="btn-pill btn-checkout" onclick="checkoutEmployee(${emp.id})">Time Out</button>` :
        `<button class="btn-pill btn-timein" onclick="markAttendance(${emp.id}, 'present')">Time In</button>`
}
```

### JWT Authentication Fix

**Issue:** API calls returning 401 (Unauthorized)
- `GET /api/attendance/employees` 
- `GET /api/attendance/stats`
- `POST /api/attendance/mark`

**Root Cause:** API endpoints require JWT token in Authorization header, but frontend was not passing it.

**Solution:**
1. Exposed JWT token from PHP session to JavaScript:
```php
const jwtToken = <?= json_encode($_SESSION['jwt_token'] ?? null) ?>;
```

2. Added Authorization header to all API fetch calls:
```javascript
fetch(`${basePath}/api/attendance/employees?branch_code=${code}&date=${date}`, {
    headers: jwtToken ? { 'Authorization': `Bearer ${jwtToken}` } : {}
})
```

### Files Modified
- `views/attendance/site_attendance.php` - Button logic, JWT auth headers, debug logging
- `controllers/AttendanceController.php` - API endpoints (already JWT-enabled)

### Debugging
Added console logging to verify JWT token availability:
```javascript
console.log('JWT Token available:', !!jwtToken, 'Token preview:', jwtToken ? jwtToken.substring(0, 20) + '...' : 'none');
```

### Notes
- JWT token is set in `$_SESSION['jwt_token']` during login via `LoginController`
- User must be logged in at the same domain/IP for session to work
- If accessing via IP address after logging in via domain, session won't transfer

---

## Bug Fixes & Feature Updates (April 17, 2026)

### 1. Branch Name Fix in Views

**Files:** `views/branch/index.php`, `views/attendance/site_attendance.php`

**Issue:** PHP warnings "Undefined array key 'branch_code'" and "Undefined array key 'branch_name'" appearing on branch listing and attendance pages.

**Root Cause:** SQL query in `models/Branch.php` was aliasing columns incorrectly:
```php
SELECT branch_code as code, branch_name as name  // Wrong
```

**Fix:**
- Removed column aliases in `models/Branch.php:9`
```php
SELECT branch_code, branch_name  // Correct
```
- Added null coalescing operators (`??`) in views for defensive coding:
```php
<?= htmlspecialchars($branch['branch_code'] ?? 'N/A') ?>
<?= htmlspecialchars($branch['branch_name'] ?? 'Unknown Branch') ?>
```

### 2. Employee Branch Assignment Feature

**Migration:** `database/migrations/add_branch_to_employees.sql`
- Added `branch_name` column to `employees` table
- Created index `idx_employee_branch` for faster queries

**Files:** `models/Employee.php`, `controllers/AttendanceController.php`

**Feature:** Attendance page now filters employees by their assigned branch when clicking a project/branch card.

**Implementation:**
```php
// Get branch name from branch code
$branch = $this->branchModel->findByCode($branchCode);
$branchName = $branch ? $branch['branch_name'] : null;

// Get employees assigned to this branch
if ($branchName) {
    $employees = $this->employeeModel->findByBranch($branchName);
    if (empty($employees)) {
        $employees = $this->employeeModel->findAll(); // Fallback
    }
}
```

**New Method:** `Employee::findByBranch($branchName)` - Returns employees assigned to a specific branch.

### 3. Auto-Update Employee Branch on Check-In

**File:** `models/Attendance.php`

**Feature:** Employee's `branch_name` in the employees table now automatically updates to reflect their most recent check-in location.

**Implementation:**
- Added `updateEmployeeBranch()` private method (lines 168-189)
- Called on successful check-in in `recordAttendance()` method (line 234)

**Logic:**
1. Employee checks in at Branch A → `employees.branch_name` = "Branch A"
2. Employee checks out at Branch A → No change (still "Branch A")
3. Employee checks in at Branch B → `employees.branch_name` = "Branch B" (updated!)

**SQL in updateEmployeeBranch():**
```sql
SELECT branch_name FROM branches WHERE branch_code = :branch_code
UPDATE employees SET branch_name = :branch_name WHERE id = :employee_id
```

This ensures the attendance page always shows employees at their most recent work location.

### 4. Finance Layout Update

**File:** `views/finance/finance_layout.php`

**Change:** Added proper nested layout rendering with output buffering to support main layout wrapper.

```php
$innerContent = $content;
ob_start();
// ... finance layout content ...
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
```

---

## Payroll System Implementation (April 17, 2026)

### Summary
Implemented a complete payroll calculation system with weekly payroll reports, employee payroll processing, and export functionality.

### Files Created

#### PayrollController (`controllers/PayrollController.php`)
- `index()` - Main payroll page with branch filter and week selection
- `calculate()` - Calculate payroll for a week and branch
- `getWeeklyData()` - Get payroll data for a specific week (saved or calculated)
- `export()` - Export payroll to CSV/Excel format
- `printPayslip()` - Generate payslip for individual employee
- `getWeekOptions()` - Get week options for year/month selection

### Routes Added (`app.php`)
```php
// Payroll routes
$router->add('finance/payroll', ['controller' => 'PayrollController', 'action' => 'index']);
$router->add('api/payroll/calculate', ['controller' => 'PayrollController', 'action' => 'calculate']);
$router->add('api/payroll/weekly', ['controller' => 'PayrollController', 'action' => 'getWeeklyData']);
$router->add('api/payroll/export', ['controller' => 'PayrollController', 'action' => 'export']);
$router->add('api/payroll/payslip', ['controller' => 'PayrollController', 'action' => 'printPayslip']);
$router->add('api/payroll/week-options', ['controller' => 'PayrollController', 'action' => 'getWeekOptions']);
```

### Payroll Features
- **Weekly Payroll Calculation**: Monday-Saturday work week
- **Automatic Deductions**: SSS, PhilHealth, Pag-IBIG contributions
- **Performance Allowance**: Configurable per employee
- **Daily Rate Based**: Calculates based on days worked
- **Branch Filtering**: View payroll by branch
- **CSV Export**: Export payroll data to Excel
- **Payslip Generation**: Individual employee payslips

---

## Attendance Audit & Site Attendance Enhancements (April 17, 2026)

### Attendance Audit Page Updates (`views/attendance/attendance_audit.php`)

#### Source Badge System
- Added source badges to show attendance entry method (QR Scan, Manual Entry)
- Hover tooltip showing full notes/details
- Updated export function to include Source column

#### Modal Calendar Improvements
- Enhanced day modal with scrollable session list
- Session items with color-coded status (green=present, orange=late)
- Session number badges, check-in/check-out times, branch names
- Total hours calculation for multi-session days

### Site Attendance Page Updates (`views/attendance/site_attendance.php`)

#### Super Admin Features
- Added super admin detection using JWT validation
- Current branch display for each employee (super admin only)

#### Interactive Stat Cards
- Clickable stat cards for filtering (Total/Present/Absent)
- Hover tooltips showing employee lists
- Active state styling with golden accent

---

## Ngrok Database Connection Fix (April 17, 2026)

### Issue
Database connection failed when using ngrok for testing with MySQL 8.4+ authentication issues.

### Solution
Added ngrok detection to database connection files to use root user for testing:

**`config/database.php` and `conn/db_connection.php`:**
```php
$isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1']) ||
           strpos($_SERVER['SERVER_NAME'] ?? '', 'localhost') !== false ||
           strpos($_SERVER['HTTP_HOST'] ?? '', 'ngrok') !== false;
```

---

## Branch Management Fixes (April 17, 2026)

### Branch Index Page (`views/branch/index.php`)
- Fixed null handling for branch codes and names
- Fixed edit/delete URLs to use dynamic base paths
- Added proper null checking for status display

### Employee Model Updates (`models/Employee.php`)
- Added `findByBranch()` method for branch-based employee queries

### Finance Layout Fix (`views/finance/finance_layout.php`)
- Fixed nested content rendering with proper output buffering

---

## Employee Schema Query Fixes (April 17, 2026)

### Summary
Reviewed and fixed all project files that reference the `employees` table to ensure compatibility with the updated schema. Fixed column name mismatches and added missing fields to queries.

### Issues Found

**1. Column Name Mismatch: `has_deductions` vs `has_deduction`**
The database column was named `has_deduction` (singular), but the code was using `has_deductions` (plural).

**2. Missing Columns in UPDATE Query**
The `Employee::update()` method was not updating the new schema columns: `middle_name`, `status`, `daily_rate`, `has_deduction`, `profile_image`.

### Files Modified

#### `models/Employee.php`
- **Line 23**: Fixed `has_deductions` → `has_deduction` in `create()` method
- **Lines 50-87**: Updated `update()` method to include all new columns:
  ```php
  $query = 'UPDATE employees SET
      employee_code = :employee_code,
      first_name = :first_name,
      middle_name = :middle_name,
      last_name = :last_name,
      email = :email,
      department = :department,
      position = :position,
      status = :status,
      daily_rate = :daily_rate,
      has_deduction = :has_deduction';
  ```
  - Added conditional `profile_image` update
  - Added proper parameter binding for all new fields

#### `controllers/EmployeeController.php`
- **Line 53**: Fixed `has_deductions` → `has_deduction` in `create()` handler
- **Line 110**: Fixed `has_deductions` → `has_deduction` in `edit()` handler

#### `views/employee/employee_list.php`
- **Lines 391-393**: Fixed display field from `has_deductions` to `has_deduction`
- **Line 476**: Fixed checkbox name in add form: `name="has_deduction"`
- **Line 588**: Fixed checkbox ID/name in edit form: `id="edit_has_deduction" name="has_deduction"`
- **Line 1083**: Fixed JavaScript reference: `employee.has_deduction`

### Verification
- Verified no remaining `has_deductions` references in the codebase
- Confirmed `core/Model.php` uses `SELECT *` queries (automatically compatible with schema changes)
- All JOIN queries in `Attendance.php` reference correct employee columns

### Compatibility Notes
The following model methods automatically work with the new schema:
- `Employee::findAll()` - Uses `SELECT *`
- `Employee::findById()` - Uses `SELECT *`
- `Employee::findByEmail()` - Uses `SELECT *`
- `Employee::findByEmployeeCode()` - Uses `SELECT *`
- `Employee::search()` - Searches across name/code/email fields
- All `Attendance` model JOINs - Reference employees table correctly

---

## Payroll Module Implementation (April 17, 2026)

### Summary
Implemented a complete payroll system for weekly salary computation with SSS, PhilHealth, and Pag-IBIG deductions.

### New Files Created

#### `controllers/PayrollController.php`
- `index()` - Main payroll page with branch/week selection
- `calculate()` - Calculate weekly payroll for branch employees  
- `getWeeklyData()` - Fetch or calculate payroll data
- `export()` - Export payroll to CSV
- `printPayslip()` - Generate payslip for employee
- `getWeekOptions()` - Get available week options for year/month

### Features

**Payroll Calculation:**
- Weekly payroll (Monday-Saturday)
- Daily rate × Days worked = Basic Pay
- Performance allowance support
- Automatic government deductions:
  - SSS contribution
  - PhilHealth contribution  
  - Pag-IBIG (HDMF) contribution
- Net Pay = Gross Pay - Total Deductions

**Export:**
- CSV export with UTF-8 BOM
- Filename: `payroll_{branchCode}_week{number}_{date}.csv`

### Attendance & Branch Updates (April 17, 2026)

#### Employee Branch Assignment
**File:** `models/Attendance.php`
- Added `updateEmployeeBranch()` - Updates employee's branch_name when they check in
- Called during `recordAttendance()` when check-in is recorded

**File:** `controllers/AttendanceController.php`
- Modified `getEmployeesByBranch()` to get employees assigned to specific branch
- Falls back to all employees if no branch assignment exists

#### UI Safety Updates
**Files:** `views/attendance/site_attendance.php`, `views/branch/index.php`
- Added null coalescing (`??`) operators for safe array access
- Prevents errors when branch data fields are missing

**File:** `models/Branch.php`
- Updated `findAll()` to select specific columns instead of `SELECT *`

#### Finance Layout Update
**File:** `views/finance/finance_layout.php`
- Fixed layout nesting using output buffering (`ob_start()` / `ob_get_clean()`)
- Properly integrates with main layout system

---

## Attendance Interface JWT Authentication (April 17, 2026)

### Summary
Enhanced the attendance interface with proper JWT authentication for API endpoints and improved button logic for time in/out actions.

### Changes Made

#### 1. JWT Token Integration (`views/attendance/site_attendance.php`)
**Problem:** API calls returning 401 Unauthorized errors.

**Solution:**
- Exposed JWT token from PHP session to JavaScript:
```php
const jwtToken = <?= json_encode($_SESSION['jwt_token'] ?? null) ?>;
console.log('JWT Token available:', !!jwtToken, 'Token preview:', jwtToken ? jwtToken.substring(0, 20) + '...' : 'none');
```

- Added Authorization header to all API fetch calls:
```javascript
fetch(`${basePath}/api/attendance/employees?branch_code=${code}&date=${date}`, {
    headers: jwtToken ? { 'Authorization': `Bearer ${jwtToken}` } : {}
})
```

**API Endpoints Updated:**
- `GET /api/attendance/employees`
- `GET /api/attendance/stats`
- `POST /api/attendance/mark`

#### 2. Attendance Button Logic Update
**File:** `views/attendance/site_attendance.php`

**New Behavior:**
| Employee State | Buttons Shown |
|---------------|-----------------|
| No check-in/check-out | Mark Absent + Time In |
| Checked in only | Time Out |
| Checked out | Time In |

**Implementation:**
```javascript
${!emp.check_in && !emp.check_out ?
    `<button class="btn-pill btn-absent" onclick="markAttendance(${emp.id}, 'absent')">Mark Absent</button>
     <button class="btn-pill btn-timein" onclick="markAttendance(${emp.id}, 'present')">Time In</button>` :
    !emp.check_out ?
        `<button class="btn-pill btn-checkout" onclick="checkoutEmployee(${emp.id})">Time Out</button>` :
        `<button class="btn-pill btn-timein" onclick="markAttendance(${emp.id}, 'present')">Time In</button>`
}
```

#### 3. Checkout Function
Added `checkoutEmployee()` function that calls `markAttendance()` with 'present' status, triggering the backend checkout logic.

### Git Commits
- `dc12984` - enhance the attendance interfaceeeeee
- `f4a2596` - enhance the attendance interfaceeeeee
- `2fa56bc` - enhance the attendance interface
- `cd75c17` - enhance the attendance interface
- `87ec6bb` - Set Token to all API endpoint

---
