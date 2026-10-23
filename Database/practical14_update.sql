ALTER TABLE admins ADD COLUMN status VARCHAR(10) NOT NULL DEFAULT 'active';

UPDATE admins SET role = 'admin';
UPDATE admins SET role = 'super_admin' WHERE admin_id = 'ADM10025';

CREATE TABLE audit_logs (
  log_id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id INT NULL,
  action VARCHAR(50) NOT NULL,
  target_table VARCHAR(50),
  target_id INT,
  details VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE audit_logs MODIFY admin_id INT NULL;
