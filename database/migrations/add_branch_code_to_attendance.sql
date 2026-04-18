-- Add branch_code column to attendance table
-- This column is required for the QR scanner and attendance tracking

ALTER TABLE attendance ADD COLUMN branch_code VARCHAR(50) NULL AFTER employee_id;

-- Add index for faster lookups
CREATE INDEX idx_attendance_branch_code ON attendance(branch_code);

-- Optional: Update existing records to have a default branch_code
-- UPDATE attendance SET branch_code = 'MAIN' WHERE branch_code IS NULL;
