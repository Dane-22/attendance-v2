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

## Next Steps (Future Enhancements)

- [ ] Add offline sync with IndexedDB
- [ ] Implement service worker for PWA
- [ ] Add sound feedback for scans
- [ ] Create attendance reports by branch
- [ ] Add branch-wise employee assignment
- [ ] Implement shift management per branch

---

