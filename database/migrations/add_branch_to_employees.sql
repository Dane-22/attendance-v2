-- Add branch_name column to employees table
ALTER TABLE employees ADD COLUMN branch_name VARCHAR(100) NULL AFTER position;

-- Add index for faster queries by branch
CREATE INDEX idx_employee_branch ON employees(branch_name);

-- Update existing employees to have a default branch if needed
-- UPDATE employees SET branch_name = 'Main Branch' WHERE branch_name IS NULL;
