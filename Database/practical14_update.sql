SET @admin_status_exists = (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'admins'
    AND COLUMN_NAME = 'status'
);
SET @admin_status_sql = IF(
  @admin_status_exists = 0,
  'ALTER TABLE admins ADD COLUMN status VARCHAR(10) NOT NULL DEFAULT ''active''',
  'SELECT 1'
);
PREPARE admin_status_statement FROM @admin_status_sql;
EXECUTE admin_status_statement;
DEALLOCATE PREPARE admin_status_statement;

UPDATE admins SET role = 'admin' WHERE role NOT IN ('admin', 'super_admin');
UPDATE admins SET role = 'super_admin' WHERE admin_id = 'ADM10025';

CREATE TABLE IF NOT EXISTS audit_logs (
  log_id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id INT NULL,
  action VARCHAR(50) NOT NULL,
  target_table VARCHAR(50),
  target_id INT,
  details VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE audit_logs MODIFY admin_id INT UNSIGNED NULL;
