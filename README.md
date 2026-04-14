# Attendance System (PHP MVC)

A complete attendance management system built with PHP using the MVC (Model-View-Controller) architecture.

## Features

- **Employee Management**: Add, edit, delete employees with employee codes
- **Daily Attendance**: Mark and view attendance for any date
- **Attendance Status**: Present, Absent, Late, Half Day, Leave
- **Check In/Out**: Record employee check-in and check-out times
- **Monthly Reports**: Generate attendance reports by month and year
- **Employee History**: View attendance history for individual employees

## Project Structure

```
├── config/               # Configuration files
│   ├── database.php      # Database connection settings
│   └── schema.sql        # Database schema
├── controllers/          # Controllers
│   ├── AttendanceController.php
│   └── EmployeeController.php
├── core/                 # Core MVC classes
│   ├── Controller.php    # Base controller
│   ├── Database.php      # Database connection class
│   ├── Model.php         # Base model
│   └── Router.php        # URL routing
├── models/               # Models
│   ├── Attendance.php    # Attendance model
│   └── Employee.php      # Employee model
├── views/                # Views
│   ├── attendance/       # Attendance views
│   ├── employee/         # Employee views
│   └── layouts/          # Layout templates
├── public/               # Public directory (entry point)
│   └── index.php         # Main entry point
├── .htaccess             # Apache rewrite rules
└── README.md             # This file
```

## Installation

1. **Create Database**:
   - Import `config/schema.sql` into your MySQL database
   - This creates the `attendance_system` database with sample employees

2. **Configure Database**:
   - Edit `config/database.php` with your database credentials:
   ```php
   return [
       'host' => 'localhost',
       'database' => 'attendance_system',
       'username' => 'root',
       'password' => ''
   ];
   ```

3. **Web Server**:
   - Point your web server to the project root
   - The `.htaccess` file handles URL rewriting to `public/index.php`
   - For Apache, ensure `mod_rewrite` is enabled

4. **Access the Application**:
   - Navigate to `http://localhost/jajr-v2/`

## Usage

### Employees
- View all employees: `/employee`
- Add employee: `/employee/create`
- Edit employee: `/employee/edit/{id}`
- View employee attendance: Click "Attendance" on employee list

### Attendance
- Today's attendance: `/attendance`
- All records: `/attendance/all`
- Add attendance: `/attendance/create`
- Edit attendance: `/attendance/edit/{id}`
- Monthly reports: `/attendance/report`

## Default Login

The system comes with sample employees:
- EMP001 - John Doe (IT, Developer)
- EMP002 - Jane Smith (HR, Manager)
- EMP003 - Bob Johnson (Sales, Representative)

## Technologies

- **PHP 7.4+**: Server-side scripting
- **MySQL**: Database
- **PDO**: Database abstraction
- **CSS**: Custom styling (no external dependencies)

## MVC Architecture

- **Models**: Handle database operations (CRUD)
- **Views**: Present data to users (templates)
- **Controllers**: Process requests, use models, render views
- **Router**: Maps URLs to controllers/actions

## Security Features

- Prepared statements (SQL injection prevention)
- XSS protection through output escaping
- CSRF protection ready (can be added)
- Input validation
