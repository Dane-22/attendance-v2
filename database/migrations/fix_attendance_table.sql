-- Fix attendance table - add all required columns for QR scanning
-- Run this on production to fix missing columns

-- Add missing columns if they don't exist
ALTER TABLE attendance 
ADD COLUMN IF NOT EXISTS employee_id INT NOT NULL,
ADD COLUMN IF NOT EXISTS branch_code VARCHAR(50) NULL,
ADD COLUMN IF NOT EXISTS date DATE NOT NULL,
ADD COLUMN IF NOT EXISTS check_in TIME NULL,
ADD COLUMN IF NOT EXISTS check_out TIME NULL,
ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'present',
ADD COLUMN IF NOT EXISTS notes TEXT NULL,
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- Add indexes for performance
CREATE INDEX IF NOT EXISTS idx_attendance_employee_date ON attendance(employee_id, date);
CREATE INDEX IF NOT EXISTS idx_attendance_branch_code ON attendance(branch_code);
