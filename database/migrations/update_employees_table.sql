-- Add missing columns to employees table

-- Add middle_name column
ALTER TABLE employees ADD COLUMN middle_name VARCHAR(100) NULL AFTER first_name;

-- Add status column with default value
ALTER TABLE employees ADD COLUMN status VARCHAR(20) DEFAULT 'Active' AFTER position;

-- Add daily_rate column
ALTER TABLE employees ADD COLUMN daily_rate DECIMAL(10,2) DEFAULT 0.00 AFTER status;

-- Add has_deductions column (boolean/tinyint)
ALTER TABLE employees ADD COLUMN has_deductions TINYINT(1) DEFAULT 0 AFTER daily_rate;

-- Add profile_image column
ALTER TABLE employees ADD COLUMN profile_image VARCHAR(255) NULL AFTER has_deductions;

-- Note: If department column doesn't exist, uncomment the line below:
-- ALTER TABLE employees ADD COLUMN department VARCHAR(100) NULL AFTER email;
