-- Add branch_code column to attendance table
ALTER TABLE attendance ADD COLUMN branch_code VARCHAR(10) NULL AFTER employee_id;

-- Add index for faster queries by branch
CREATE INDEX idx_attendance_branch ON attendance(branch_code);

-- Add index for employee + date + branch queries (for duplicate checking)
CREATE INDEX idx_attendance_emp_date_branch ON attendance(employee_id, date, branch_code);

-- Add foreign key constraint (optional - if you want strict referential integrity)
-- ALTER TABLE attendance ADD CONSTRAINT fk_attendance_branch 
--     FOREIGN KEY (branch_code) REFERENCES branches(branch_code);
