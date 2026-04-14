-- Create branches table
CREATE TABLE IF NOT EXISTS branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_code VARCHAR(10) NOT NULL UNIQUE,
    branch_name VARCHAR(100) NOT NULL,
    address TEXT NULL,
    contact_number VARCHAR(20) NULL,
    status VARCHAR(20) DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert initial branches
INSERT INTO branches (branch_code, branch_name, status) VALUES
('A', 'Sto. Rosario', 'Active'),
('B', 'BCDA', 'Active'),
('C', 'Sundara', 'Active'),
('D', 'Panicsican', 'Active'),
('E', 'Main Office', 'Active'),
('F', 'Capitol', 'Active');
