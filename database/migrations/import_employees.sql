-- =====================================================
-- EMPLOYEE IMPORT MIGRATION SCRIPT
-- Run this in TablePlus SQL Editor
-- =====================================================

-- Step 1: Backup existing employees (safety first)
-- CREATE TABLE employees_backup AS SELECT * FROM employees;

-- Step 2: Extend existing column lengths to match source data
ALTER TABLE employees 
  MODIFY employee_code VARCHAR(50),
  MODIFY first_name VARCHAR(100),
  MODIFY last_name VARCHAR(100),
  MODIFY email VARCHAR(100);

-- Step 3: Add missing columns from source schema (run each separately if needed)
ALTER TABLE employees ADD COLUMN middle_name VARCHAR(100) NULL AFTER last_name;
ALTER TABLE employees ADD COLUMN status VARCHAR(50) DEFAULT 'Active' AFTER position;
ALTER TABLE employees ADD COLUMN profile_image VARCHAR(255) NULL;
ALTER TABLE employees ADD COLUMN daily_rate DECIMAL(10,2) DEFAULT 600.00;
ALTER TABLE employees ADD COLUMN performance_allowance DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE employees ADD COLUMN has_deduction TINYINT(1) DEFAULT 1;
ALTER TABLE employees ADD COLUMN branch_id INT(11) NULL;

-- Step 4: Import employees from source data
-- Note: Using INSERT IGNORE to skip duplicates (existing employee_code/email)
-- department field will be derived from position

INSERT IGNORE INTO employees 
  (employee_code, first_name, middle_name, last_name, email, department, position, 
   status, profile_image, daily_rate, performance_allowance, has_deduction, branch_id)
VALUES
  ('SA001', 'Super', 'Torres', 'Admin', 'admin@jajrconstruction.com', 'Administration', 'Super Admin', 'Active', 'uploads/profile_images/profile_6_1771480314.png', 600.00, 0.00, 0, 33),
  ('E0001', 'AARIZ', '', 'MARLOU', 'aariz.marlou@example.com', 'Operations', 'Worker', 'Active', 'profile_69d6006a66bfe6.32302616.png', 700.00, 0.00, 0, 21),
  ('E0002', 'CESAR', '', 'ABUBO', 'cesar.abubo@example.com', 'Operations', 'Worker', 'Active', 'profile_69d60010402ca2.92249984.png', 550.00, 150.00, 1, 21),
  ('E0003', 'MARLON', '', 'AGUILAR', 'marlon.aguilar@example.com', 'Operations', 'Worker', 'Active', 'profile_69d600211a0589.35341824.png', 600.00, 100.00, 0, 10),
  ('E0004', 'NOEL', NULL, 'ARIZ', 'noel.ariz@example.com', 'Operations', 'Worker', 'Inactive', NULL, 550.00, 0.00, 1, 26),
  ('E0005', 'DANIEL', '', 'BACHILLER', 'daniel.bachiller@example.com', 'Operations', 'Worker', 'Active', 'profile_69d6002e97f1d3.80387073.png', 600.00, 100.00, 1, 21),
  ('E0006', 'ALFREDO', '', 'BAGUIO', 'alfredo.baguio@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5ff418361b7.89098507.png', 550.00, 150.00, 0, 21),
  ('E0007', 'ROLLY', '', 'BALTAZAR', 'rolly.baltazar@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5ff547f48e9.55971784.png', 500.00, 0.00, 0, 21),
  ('E0008', 'DONG', NULL, 'BAUTISTA', 'dong.bautista@example.com', 'Operations', 'Worker', 'Inactive', NULL, 600.00, 0.00, 1, 20),
  ('E0009', 'JANLY', '', 'BELINO', 'janly.belino@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5f8bd3ff0e7.72784110.png', 650.00, 0.00, 0, 10),
  ('E0010', 'MENUEL', '', 'BENITEZ', 'menuel.benitez@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5f8d8982db4.66850139.png', 600.00, 100.00, 1, 21),
  ('E0011', 'GELMAR', '', 'BARNACHEA', 'gelmar.bernachea@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5ff3620afe4.25764722.png', 500.00, 0.00, 0, 21),
  ('E0012', 'JOMAR', NULL, 'CABANBAN', 'jomar.cabanban@example.com', 'Operations', 'Worker', 'Inactive', NULL, 600.00, 0.00, 1, 22),
  ('E0013', 'MARIO', '', 'CABANBAN', 'mario.cabanban@example.com', 'Operations', 'Worker', 'Active', 'profile_69d9bdfcd6a4e1.58343645.png', 600.00, 100.00, 0, 10),
  ('E0014', 'KELVIN', NULL, 'CALDERON', 'kelvin.calderon@example.com', 'Operations', 'Worker', 'Inactive', NULL, 500.00, 0.00, 1, 21),
  ('E0015', 'FLORANTE', NULL, 'CALUZA', 'florante.caluza@example.com', 'Operations', 'Worker', 'Inactive', NULL, 600.00, 0.00, 0, 22),
  ('E0016', 'MELVIN', NULL, 'CAMPOS', 'melvin.campos@example.com', 'Operations', 'Worker', 'Inactive', NULL, 600.00, 0.00, 1, 21),
  ('E0017', 'JERWIN', '', 'CAMPOS', 'jerwin.campos@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5ff06eb31e2.16953567.png', 550.00, 150.00, 1, 21),
  ('E0018', 'BENJIE', '', 'CARAS', 'benjie.caras@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5ffdbd4db63.91949381.png', 700.00, 0.00, 0, 21),
  ('E0019', 'JORELLE BONJO', '', 'DACUMOS', 'bonjo.dacumos@example.com', 'Operations', 'Worker', 'Active', 'profile_69d60206afa450.64233705.png', 500.00, 0.00, 0, 10),
  ('E0020', 'RYAN', '', 'DEOCARIS', 'ryan.deocaris@example.com', 'Operations', 'Worker', 'Active', 'profile_69d6009b3d7d21.77206328.png', 500.00, 0.00, 0, 21),
  ('E0021', 'BEN', '', 'ESTEPA', 'ben.estepa@example.com', 'Operations', 'Worker', 'Active', 'profile_69d6007aeb1ce2.19714221.png', 600.00, 600.00, 1, 21),
  ('E0022', 'MAR DAVE', '', 'FLORES', 'mardave.flores@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5ffa98b1854.65713856.png', 550.00, 150.00, 0, 10),
  ('E0023', 'ALBERT', '', 'FONTANILLA', 'albert.fontanilla@example.com', 'Operations', 'Worker', 'Active', 'profile_69d600ff0c9b92.81545089.png', 550.00, 150.00, 1, 21),
  ('E0024', 'JOHN WILSON', NULL, 'FONTANILLA', 'johnwilson.fontanilla@example.com', 'Operations', 'Worker', 'Inactive', NULL, 600.00, 0.00, 0, 20),
  ('E0025', 'LEO', '', 'GURTIZA', 'leo.gurtiza@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5fec772d144.20772071.png', 600.00, 100.00, 1, 10),
  ('E0026', 'JOSE', '', 'IGLECIAS', 'jose.iglecias@example.com', 'Operations', 'Worker', 'Active', 'profile_69d9afab0cf298.43125381.png', 500.00, 0.00, 0, 31),
  ('E0027', 'JEFFREY', '', 'JIMENEZ', 'jeffrey.jimenez@example.com', 'Operations', 'Worker', 'Active', 'profile_69d6008a7d4189.24345782.png', 550.00, 150.00, 1, 21),
  ('E0028', 'WILSON', '', 'LICTAOA', 'wilson.lictaoa@example.com', 'Operations', 'Worker', 'Inactive', NULL, 500.00, 0.00, 1, 21),
  ('E0029', 'LORETO', '', 'MABALO', 'loreto.mabalo@example.com', 'Operations', 'Worker', 'Active', 'profile_69d9bddccd1619.96311862.png', 600.00, 100.00, 0, 10),
  ('E0030', 'ROMEL', '', 'MALLARE', 'romel.mallare@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5fea1eb47d3.35526436.png', 800.00, 150.00, 1, 31),
  ('E0031', 'SAMUEL SR.', '', 'MARQUEZ', 'samuel.marquez@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5fe62cbdd09.62445973.png', 500.00, 0.00, 0, 21),
  ('E0032', 'ROLLY', NULL, 'MARZAN', 'rolly.marzan@example.com', 'Operations', 'Worker', 'Inactive', NULL, 600.00, 0.00, 1, 10),
  ('E0033', 'RONALD', '', 'MARZAN', 'ronald.marzan@example.com', 'Operations', 'Worker', 'Active', 'profile_69d9bdf04c57f8.40601532.png', 600.00, 1000.00, 0, 10),
  ('E0034', 'WILSON', '', 'MARZAN', 'wilson.marzan@example.com', 'Operations', 'Worker', 'Active', 'profile_69d6004781b584.57723505.png', 600.00, 100.00, 1, 10),
  ('E0035', 'MARVIN', NULL, 'MIRANDA', 'marvin.miranda@example.com', 'Operations', 'Worker', 'Inactive', NULL, 600.00, 0.00, 0, 22),
  ('E0036', 'JOE', '', 'MONTERDE', 'joe.monterde@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5ff67b7ece6.83173563.png', 700.00, 0.00, 0, 21),
  ('E0038', 'ARNOLD', '', 'NERIDO', 'arnold.nerido@example.com', 'Operations', 'Worker', 'Active', NULL, 600.00, 100.00, 0, 31),
  ('E0040', 'DANNY', '', 'PADILLA', 'danny.padilla@example.com', 'Operations', 'Worker', 'Active', 'profile_69d600ac33ec53.26400528.png', 500.00, 0.00, 0, 10),
  ('E0041', 'EDGAR', NULL, 'PANEDA', 'edgar.paneda@example.com', 'Operations', 'Worker', 'Inactive', NULL, 550.00, 0.00, 1, 26),
  ('E0042', 'JEREMY', '', 'PIMENTEL', 'jeremy.pimentel@example.com', 'Operations', 'Worker', 'Active', 'profile_69d600d6b1d057.48967611.png', 550.00, 0.00, 0, 21),
  ('E0043', 'MIGUEL', NULL, 'PREPOSI', 'miguel.preposi@example.com', 'Operations', 'Worker', 'Active', NULL, 600.00, 100.00, 1, 10),
  ('E0044', 'JUN', NULL, 'ROAQUIN', 'jun.roaquin@example.com', 'Operations', 'Worker', 'Inactive', NULL, 600.00, 0.00, 1, 26),
  ('E0045', 'RICKMAR', '', 'SANTOS', 'rickmar.santos@example.com', 'Operations', 'Worker', 'Active', 'profile_69d600eed64931.69263448.png', 500.00, 100.00, 1, 28),
  ('E0046', 'RIO', '', 'SILOY', 'rio.siloy@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5fe758e89a2.19541693.png', 750.00, 150.00, 1, 32),
  ('E0047', 'NORMAN', '', 'TARAPE', 'norman.tarape@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5fe90ac00d1.71248253.png', 500.00, 0.00, 0, 10),
  ('E0048', 'HILMAR', '', 'TATUNAY', 'hilmar.tatunay@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5ff866f3104.37734210.png', 500.00, 100.00, 1, 21),
  ('E0049', 'KENNETH JOHN', '', 'UGAS', 'kennethjohn.ugas@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5ff943a6d70.65129657.png', 600.00, 50.00, 1, 10),
  ('E0050', 'CLYDE JUSTINE', NULL, 'VASADRE', 'clydejustine.vasadre@example.com', 'Operations', 'Worker', 'Inactive', NULL, 500.00, 0.00, 1, 28),
  ('ENG-2026-0005', 'JOYLENE F.', NULL, 'BALANON', 'joylene.balanon@example.com', 'Engineering', 'Engineer', 'Active', NULL, 600.00, 0.00, 0, 33),
  ('ENG-2026-0002', 'John Kennedy', '', 'Lucas', 'lucas@gmail.com', 'Engineering', 'Engineer', 'Active', NULL, 600.00, 0.00, 0, 10),
  ('ENG-2026-0003', 'Julius John', '', 'Echague', 'echague@gmail.com', 'Engineering', 'Engineer', 'Inactive', NULL, 600.00, 0.00, 1, 21),
  ('PRO-2026-0001', 'Junell', '', 'Tadina', 'tadina@gmail.com', 'Engineering', 'Engineer', 'Active', NULL, 600.00, 0.00, 0, 33),
  ('ENG-2026-0006', 'Winnielyn Kaye', '', 'Olarte', 'olarte@gmail.com', 'Engineering', 'Engineer', 'Active', NULL, 600.00, 0.00, 0, 33),
  ('ADMIN-2026-0002', 'RONALYN', NULL, 'MALLARE', 'ronalyn.mallare@example.com', 'Administration', 'Admin', 'Active', NULL, 600.00, 0.00, 0, 33),
  ('ENG-2026-0001', 'MICHELLE F.', NULL, 'NORIAL', 'michelle.norial@example.com', 'Engineering', 'Engineer', 'Active', NULL, 600.00, 0.00, 0, 33),
  ('ADMIN-2026-0001', 'Elaine', 'Torres', 'Aguilar', 'aguilar@gmail.com', 'Administration', 'Admin', 'Active', 'profile_6996a4f55d7335.10207456.png', 600.00, 0.00, 0, 33),
  ('SA-2026-002', 'Jason', 'Larkin', 'Wong', 'wong@gmail.com', 'Administration', 'Admin', 'Active', NULL, 600.00, 0.00, 0, NULL),
  ('SA-2026-003', 'Lee Aldrich', '', 'Rimando', 'rimando@gmail.com', 'Administration', 'Admin', 'Active', NULL, 600.00, 0.00, 0, NULL),
  ('SA-2026-004', 'Marc Justin', '', 'Arzadon', 'arzadon@gmail.com', 'Administration', 'Admin', 'Active', NULL, 600.00, 0.00, 0, NULL),
  ('E0052', 'JOSHUA', NULL, 'ARQUITOLA', 'joshua.arquitola@example.com', 'Operations', 'Worker', 'Inactive', NULL, 600.00, 0.00, 1, 22),
  ('E0053', 'VERGEL', '', 'DACUMOS', 'vergel.dacumos@example.com', 'Operations', 'Worker', 'Inactive', NULL, 550.00, 0.00, 1, 22),
  ('E0054', 'REAL RAIN', NULL, 'IVERSON', 'realrain.iverson@example.com', 'Operations', 'Worker', 'Inactive', NULL, 600.00, 0.00, 1, 22),
  ('E0055', 'VOHANN', '', 'MIRANDA', 'vohann.miranda@example.com', 'Operations', 'Worker', 'Inactive', NULL, 550.00, 0.00, 1, 22),
  ('E0056', 'SONNY', NULL, 'OCCIANO', 'sonny.occiano@example.com', 'Operations', 'Worker', 'Inactive', NULL, 1400.00, 0.00, 1, 21),
  ('E0065', 'RANDY', '', 'ATON', 'randy.aton@example.com', 'Operations', 'Worker', 'Active', 'profile_69d600c4792567.58068989.png', 600.00, 50.00, 1, 21),
  ('E0058', 'JHUNEL', '', 'CANCHO', 'jhunel.cancho@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5fe54d05ff6.44033214.png', 500.00, 0.00, 0, 32),
  ('E0060', 'HECTOR', NULL, 'PADICLAS', 'hector.padiclas@example.com', 'Operations', 'Worker', 'Active', NULL, 600.00, 100.00, 0, 10),
  ('E0061', 'MARIANO', NULL, 'NERIDO', 'mariano.nerido@example.com', 'Operations', 'Worker', 'Active', NULL, 600.00, 0.00, 0, 10),
  ('E0062', 'JAYSON KENNETH', NULL, 'PADILLA', 'jaysonkenneth.padilla@example.com', 'Operations', 'Worker', 'Inactive', NULL, 500.00, 0.00, 1, 21),
  ('E0063', 'JEFFREY', '', 'ZAMORA', 'jeffrey.zamora@example.com', 'Operations', 'Worker', 'Active', 'profile_69d601095e8562.71487068.png', 600.00, 100.00, 0, 31),
  ('E0064', 'FRANKIE', NULL, 'PADILLA', 'frankie.padilla@example.com', 'Operations', 'Worker', 'Active', NULL, 500.00, 0.00, 0, 21),
  ('E0066', 'ROMEO', '', 'GURION', 'romeo.gurion@example.com', 'Operations', 'Worker', 'Active', 'profile_69d5ff1d4c6693.09123495.png', 550.00, 0.00, 0, 10),
  ('ADMIN-2026-0003', 'Charisse', 'Abaya', 'Laureaga', 'charisse@gmail.com', 'Administration', 'Admin', 'Active', NULL, 600.00, 0.00, 0, 33),
  ('ADMIN-2026-0004', 'Marjorie', '', 'Garcia', 'garcia@gmail.com', 'Administration', 'Admin', 'Active', NULL, 600.00, 0.00, 0, 33),
  ('ENG-2026-0007', 'Earl Cleint', 'Ordono', 'Nisperos', 'nisperos@gmail.com', 'Engineering', 'Engineer', 'Active', NULL, 600.00, 0.00, 0, 21),
  ('IT-2026-01', 'Daniel ', 'Obaldo', 'Rillera', 'danrillera.va@gmail.com', 'IT', 'Developer', 'Active', NULL, 600.00, 0.00, 0, 33),
  ('IT-2026-02', 'Prince Christiane', 'Borja', 'Tolentino', 'tolentinochristian89@gmail.com', 'IT', 'Developer', 'Active', NULL, 600.00, 0.00, 0, 33),
  ('E0067', 'Gilbert', '', 'Avecilla', 'avecilla@gmail.com', 'Operations', 'Worker', 'Inactive', NULL, 600.00, 0.00, 1, NULL),
  ('E0068', 'Joseph', '', 'Espanto', 'espanto@gmail.com', 'Operations', 'Worker', 'Active', 'profile_69d9af93b4b563.99389483.png', 550.00, 0.00, 0, 21),
  ('E0069', 'Ronel', '', 'Noces', 'noces@gmail.com', 'Operations', 'Worker', 'Active', 'profile_69d5fe420625c5.31868763.png', 500.00, 0.00, 0, 10),
  ('E0070', 'Fernando', '', 'Rivera', 'rivera@gmail.com', 'Operations', 'Worker', 'Active', 'profile_69d600e353fb09.09593138.png', 700.00, 0.00, 0, 21),
  ('E00070', 'Darwin', '', 'Gurion', 'gurion1@gmail.com', 'Operations', 'Worker', 'Active', 'profile_69d5fed995d947.19342413.png', 700.00, 0.00, 0, 10),
  ('E0071', 'Rey', '', 'Gurion', 'gurion2@gmail.com', 'Operations', 'Worker', 'Active', 'profile_69d5feeb0d97b1.11056357.png', 700.00, 0.00, 0, 10),
  ('E0072', 'Santi', '', 'Abubo', 'abubo1@gmail.com', 'Operations', 'Worker', 'Active', 'profile_69d5ffe6e6d766.98386818.png', 550.00, 0.00, 0, 21),
  ('ADMIN-2026-0005', 'Lyra', '', 'Javonillo', 'javonillo@gmail.com', 'Administration', 'Admin', 'Active', NULL, 600.00, 0.00, 0, 33),
  ('E0073', 'Sonny', '', 'Pascua', 'sonny@gmail.com', 'Operations', 'Worker', 'Inactive', NULL, 500.00, 0.00, 1, NULL),
  ('E0074', 'Edwin', '', 'Laforteza', 'edwin@gmail.com', 'Operations', 'Worker', 'Inactive', NULL, 500.00, 0.00, 1, NULL),
  ('E0075', 'Semy', '', 'Abat', 'abat@gmail.com', 'Operations', 'Worker', 'Inactive', 'profile_69c72508562873.21033310.png', 550.00, 0.00, 1, NULL),
  ('E0076', 'Reynaldo', '', 'Gurion', 'gurion@gmail.com', 'Operations', 'Worker', 'Active', NULL, 700.00, 0.00, 0, NULL),
  ('E0077', 'Larry', '', 'Gurion', 'larry@gmail.com', 'Operations', 'Worker', 'Active', 'profile_69d9aff8f24610.75781313.png', 700.00, 0.00, 0, 10),
  ('E0078', 'Kyle', '', 'Arrieta', 'kyle@gmail.com', 'Operations', 'Worker', 'Active', NULL, 550.00, 0.00, 0, 21),
  ('E0079', 'Rolan', '', 'Estrada', 'estrada@gmail.com', 'Operations', 'Worker', 'Active', NULL, 600.00, 0.00, 0, 31),
  ('E0080', 'Ronald', '', 'Estrada', 'ronald@gmail.com', 'Operations', 'Worker', 'Active', NULL, 600.00, 0.00, 0, 31),
  ('E0081', 'Arlene', '', 'Catbagan', 'cat@gmail.com', 'Operations', 'Worker', 'Inactive', NULL, 600.00, 0.00, 1, NULL),
  ('E0082', 'Test', '', 'Worker', 'testworker@gmail.com', 'Operations', 'Worker', 'Active', NULL, 600.00, 90.00, 0, 21),
  ('E0083', 'Wilben', '', 'Gurion', 'gurion5@gmail.com', 'Operations', 'Worker', 'Active', NULL, 500.00, 0.00, 0, 10),
  ('E0084', 'Rodel', '', 'Ochoco', 'ochoco@gmail.com', 'Operations', 'Worker', 'Active', NULL, 500.00, 0.00, 0, 10),
  ('E0085', 'Justine', '', 'Iglesias', 'Iglesias2@gmail.com', 'Operations', 'Worker', 'Active', NULL, 500.00, 0.00, 0, 21),
  ('E0086', 'Jhonrey', '', 'Danao', 'danao@gmail.com', 'Operations', 'Worker', 'Active', NULL, 500.00, 0.00, 0, 21),
  ('E0087', 'Marvin', '', 'Mirandan', 'miranda@gmail.com', 'Operations', 'Worker', 'Active', NULL, 600.00, 0.00, 0, 24),
  ('E0088', 'SONNY', '', 'OCCIANO', 'occiano@gmail.com', 'Operations', 'Worker', 'Active', NULL, 1400.00, 0.00, 0, 24),
  ('E0089', 'GIN TYRONE', '', 'AQUINO', 'aquino@gmail.com', 'Operations', 'Worker', 'Active', NULL, 500.00, 0.00, 0, 21),
  ('E0090', 'EFREN JAY', '', 'MORALES', 'morales@gmail.com', 'Operations', 'Worker', 'Active', NULL, 500.00, 0.00, 0, 21);

-- Step 5: Verification queries (run these after import)
-- SELECT COUNT(*) as total_employees FROM employees;
-- SELECT position, COUNT(*) as count FROM employees GROUP BY position;
-- SELECT status, COUNT(*) as count FROM employees GROUP BY status;
-- SELECT * FROM employees WHERE employee_code IN ('E0001', 'SA001', 'IT-2026-01') LIMIT 5;
