# JAJR Attendance System Guide

## Overview

The JAJR Attendance System is a web-based employee attendance tracking system with QR code scanning capabilities. It supports multi-branch operations and provides both manual and automated attendance recording methods.

---

## System Architecture

### User Roles

| Role | Description |
|------|-------------|
| **Super Admin** | Full system access, manages all branches and employees |
| **Admin** | Limited administrative access |
| **Branch** | Branch device account for QR scanner login only |

---

## Attendance Recording Flow

### 1. QR Code Scanning Flow

```
Employee scans QR at branch device
         ↓
BranchQRController::processScan()
         ↓
┌─────────────────────┐
│ 1. Validate branch   │  ← Checks $_SESSION['branch_code']
│    session          │
└─────────────────────┘
         ↓
┌─────────────────────┐
│ 2. Parse QR data   │  ← Extracts employee ID from QR
│    & find employee │
└─────────────────────┘
         ↓
┌─────────────────────┐
│ 3. Cross-branch    │  ← Checks if employee already
│    validation      │    checked in at different branch
└─────────────────────┘
         ↓
┌─────────────────────┐
│ 4. Record via      │  ← Unified method: check-in OR
│    recordAttendance│    check-out based on existing record
└─────────────────────┘
         ↓
    [Success/Error Response]
```

**Key Logic:**
- If employee has unchecked attendance today → **Check Out**
- If employee has no attendance OR already checked out → **Check In** (new session)

### 2. Manual Time In/Out Flow

```
Admin clicks Time In/Out button in dashboard
         ↓
AttendanceController::markAttendance()
         ↓
┌─────────────────────┐
│ 1. Validate JWT    │  ← Admin authentication
└─────────────────────┘
         ↓
┌─────────────────────┐
│ 2. Determine action │  ← Same unified logic as QR
│    via             │    recordAttendance()
│    recordAttendance│
└─────────────────────┘
         ↓
    [Success/Error JSON Response]
```

---

## Unified Attendance Recording

Both QR and Manual flows use the **same method**: `Attendance::recordAttendance()`

### Method Signature
```php
public function recordAttendance(
    $employeeId,   // Employee ID
    $branchCode,   // Where attendance is recorded
    $date,         // Date (Y-m-d)
    $source,       // 'qr' or 'manual'
    $currentTime   // Optional, defaults to now
)
```

### Logic Flow

```
Get last attendance for employee today
         ↓
    ┌──────────┐
    │ Has record?│
    └────┬─────┘
       │
   ┌───┴────┐
   │ YES    │          │ NO           │
   ↓        │          ↓              │
Checked    │    Create NEW check-in │
out?       │    (first time today)  │
   │        │                         │
┌──┴───┐   │                         │
│ YES  │   │                         │
↓      │   │                         │
Create │   │                         │
NEW    │   │                         │
check-in│  │                         │
   │   │   │                         │
┌──┴──┐│   │                         │
│ NO  ││   │                         │
↓     ││   │                         │
Update││   │                         │
check-││   │                         │
out   ││   │                         │
└─────┘│   │                         │
       └───┴─────────────────────────┘
```

### Source Tracking

| Source | Notes Format | Icon in Audit |
|--------|-------------|---------------|
| **QR** | `QR Scan at {branch_code}` | 🔲 QR Code |
| **Manual** | `Manual entry at {branch_code}` | 👆 Hand Pointer |

---

## Cross-Branch Validation

Prevents employees from checking in at multiple branches simultaneously.

### Validation Rule
```
IF employee has unchecked attendance at Branch A
AND tries to scan at Branch B
→ BLOCK with error: "Must check out from Branch A first"
```

### Implementation Location
- `BranchQRController::processScan()` lines 183-191

---

## Timezone Handling

All timestamps use **Philippines Time (Asia/Manila, UTC+8)**.

### Files with Timezone Set

| File | Method |
|------|--------|
| `models/Attendance.php` | `recordAttendance()` |
| `controllers/AttendanceController.php` | `markAttendance()`, `getTodayStats()` |
| `controllers/BranchQRController.php` | `processScan()` |

---

## Attendance Audit Page

**Location:** `/attendance/audit`

### Features
- **Real-time stats** (Present, Absent, Total Workers)
- **Source indicators** (QR vs Manual icons)
- **Date filtering**
- **CSV export** with source column
- **Status badges** (Present, Absent, Late)

### Table Columns
1. Employee Name
2. Employee Code
3. Branch
4. Time In
5. Time Out
6. Hours Worked
7. Status
8. **Source** (QR/Manual)
9. Actions

---

## Branch Device Login

**Location:** `/branch/login`

### Credentials Format
- **Username:** `branch-{branch_code}` (auto-generated)
- **Password:** Set when creating branch
- **Role:** `branch`

### Login Flow
```
Branch device accesses /branch/login
         ↓
Enter username (branch-A) & password
         ↓
Validate against admins table
         ↓
Set $_SESSION['branch_code'] = 'A'
         ↓
Redirect to /branch (QR scanner interface)
```

---

## Database Schema (Key Tables)

### employees
- `id`, `employee_code`, `first_name`, `last_name`, `email`, `department`, `position`

### attendance
- `id`, `employee_id`, `branch_code`, `date`, `check_in`, `check_out`, `status`, `notes`

### branches
- `id`, `branch_code`, `branch_name`, `address`, `contact_number`, `status`

### admins
- `id`, `username`, `password`, `name`, `email`, `role`, `branch_code`

---

## API Endpoints

### Attendance
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/attendance/mark` | POST | Record manual attendance |
| `/attendance/today-stats` | GET | Get today's statistics |
| `/attendance/all` | GET | List all attendance records |

### Branch QR
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/branch/scan` | POST | Process QR scan |

### Branches
| Endpoint | Method | Description |
|----------|--------|-------------|
| `/branches` | GET | List branches |
| `/branches/create` | GET/POST | Create branch |
| `/branches/edit/{id}` | GET/POST | Edit branch |
| `/branches/delete/{id}` | GET | Delete branch |

---

## Security Features

1. **JWT Authentication** - Admin actions require valid JWT token
2. **Session-based Auth** - Branch devices use PHP sessions
3. **Password Hashing** - All passwords stored with `password_hash()` (BCRYPT)
4. **Cross-Branch Validation** - Prevents attendance fraud
5. **Input Sanitization** - All user inputs escaped with `htmlspecialchars()`

---

## Common Workflows

### Adding a New Branch

1. Go to `/branches`
2. Click **Add Branch** button
3. Branch code auto-generates (A, B, C...)
4. Fill branch name, address, contact
5. Set password for branch device login
6. Save
7. System auto-creates admin account: `branch-{code}`

### Employee QR Check-In

1. Employee opens their QR code
2. Branch device scans QR
3. System records check-in with `notes: "QR Scan at {branch}"`
4. Employee works
5. Employee scans QR again to check out

### Manual Attendance Correction

1. Admin logs in to dashboard
2. Navigates to attendance audit page
3. Finds employee record
4. Uses Time In/Out buttons to record
5. System records with `notes: "Manual entry at {branch}"`

---

## Troubleshooting

### Employee Can't Check In at Different Branch
**Cause:** Already checked in at another branch without checking out
**Solution:** Employee must return to original branch and check out, or admin manually records check-out

### QR Scan Not Working
**Check:**
- Branch device logged in? (`$_SESSION['branch_code']` set)
- Employee QR code valid?
- Database connection active?

### Timezone Issues
**Check:** All files have `date_default_timezone_set('Asia/Manila')` at method start

---

## File Structure

```
jajr-v2/
├── controllers/
│   ├── AttendanceController.php    # Manual attendance actions
│   ├── BranchController.php        # Branch CRUD
│   └── BranchQRController.php      # QR scanning logic
├── models/
│   ├── Attendance.php              # recordAttendance() method
│   ├── Branch.php                  # Branch data access
│   └── Admin.php                   # Admin/branch auth
├── views/
│   ├── attendance/
│   │   └── attendance_audit.php    # Audit page with source icons
│   ├── branch/
│   │   ├── index.php               # Branch list
│   │   ├── create.php              # Add branch form
│   │   └── edit.php                # Edit branch form
│   └── auth/
│       └── branch-login.php        # Branch device login
└── ATTENDANCE_SYSTEM_GUIDE.md      # This file
```

---

## Recent Updates

- ✅ Unified QR and Manual attendance logic
- ✅ Philippines timezone (UTC+8) enforced
- ✅ Source tracking (QR vs Manual) in audit
- ✅ Auto-incrementing branch codes (A, B, C...)
- ✅ Branch password management with eye toggle
- ✅ Cross-branch validation
