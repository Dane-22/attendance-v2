-- Add branch_code column to admins table for branch device login
ALTER TABLE admins ADD COLUMN branch_code VARCHAR(10) NULL;

-- Add index for faster branch lookups
CREATE INDEX idx_admins_branch ON admins(branch_code);

-- Insert branch device accounts (optional - you can also create these via UI)
-- Example: Create a branch device account for each branch
-- INSERT INTO admins (username, password, name, role, branch_code, status) VALUES
-- ('branch-a', '$2y$10$hash...', 'Branch A Device', 'branch', 'A', 'Active'),
-- ('branch-b', '$2y$10$hash...', 'Branch B Device', 'branch', 'B', 'Active'),
-- ('branch-c', '$2y$10$hash...', 'Branch C Device', 'branch', 'C', 'Active');
