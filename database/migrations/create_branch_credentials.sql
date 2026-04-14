-- Create branch device accounts (Password: branch123)
-- All accounts use the same password for simplicity: "branch123"
-- Password hash generated with: password_hash('branch123', PASSWORD_BCRYPT)

INSERT INTO admins (username, password, name, email, role, branch_code) VALUES
('branch-a', '$2y$10$3ytjyW/KOW/muKN3yUXB9edeoZrtRqVIpHpHk8/JTXADvnT9wRzcC', 'Branch A Device - Sto. Rosario', 'branch-a@jajr.local', 'branch', 'A'),
('branch-b', '$2y$10$3ytjyW/KOW/muKN3yUXB9edeoZrtRqVIpHpHk8/JTXADvnT9wRzcC', 'Branch B Device - BCDA', 'branch-b@jajr.local', 'branch', 'B'),
('branch-c', '$2y$10$3ytjyW/KOW/muKN3yUXB9edeoZrtRqVIpHpHk8/JTXADvnT9wRzcC', 'Branch C Device - Sundara', 'branch-c@jajr.local', 'branch', 'C'),
('branch-d', '$2y$10$3ytjyW/KOW/muKN3yUXB9edeoZrtRqVIpHpHk8/JTXADvnT9wRzcC', 'Branch D Device - Panicsican', 'branch-d@jajr.local', 'branch', 'D'),
('branch-e', '$2y$10$3ytjyW/KOW/muKN3yUXB9edeoZrtRqVIpHpHk8/JTXADvnT9wRzcC', 'Branch E Device - Main Office', 'branch-e@jajr.local', 'branch', 'E'),
('branch-f', '$2y$10$3ytjyW/KOW/muKN3yUXB9edeoZrtRqVIpHpHk8/JTXADvnT9wRzcC', 'Branch F Device - Capitol', 'branch-f@jajr.local', 'branch', 'F');
