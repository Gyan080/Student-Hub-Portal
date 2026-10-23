CREATE DATABASE IF NOT EXISTS studenthub
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE studenthub;

CREATE TABLE IF NOT EXISTS students (
  student_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  student_uid VARCHAR(50) NOT NULL,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  dob DATE NOT NULL,
  gender VARCHAR(10) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  address VARCHAR(255) NOT NULL,
  course VARCHAR(100) NOT NULL,
  study_year VARCHAR(100) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (student_id),
  UNIQUE KEY uq_students_student_uid (student_uid),
  UNIQUE KEY uq_students_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS events (
  event_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  event_date DATE NOT NULL,
  location VARCHAR(255) NOT NULL,
  fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  category VARCHAR(50) NOT NULL,
  status VARCHAR(10) NOT NULL DEFAULT 'open',
  poster VARCHAR(255) NULL,
  PRIMARY KEY (event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  admin_id VARCHAR(8) NOT NULL,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(100) NOT NULL DEFAULT 'Department Head / Principal',
  department VARCHAR(100) NOT NULL DEFAULT 'Information Technology',
  status VARCHAR(10) NOT NULL DEFAULT 'active',
  PRIMARY KEY (id),
  UNIQUE KEY uq_admins_admin_id (admin_id),
  UNIQUE KEY uq_admins_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS registrations (
  registration_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  student_id INT UNSIGNED NOT NULL,
  event_id INT UNSIGNED NOT NULL,
  registered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (registration_id),
  UNIQUE KEY uq_registrations_student_event (student_id, event_id),
  CONSTRAINT fk_registrations_student
    FOREIGN KEY (student_id) REFERENCES students (student_id)
    ON DELETE CASCADE,
  CONSTRAINT fk_registrations_event
    FOREIGN KEY (event_id) REFERENCES events (event_id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
  log_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  admin_id INT UNSIGNED NULL,
  action VARCHAR(50) NOT NULL,
  target_table VARCHAR(50) NULL,
  target_id INT NULL,
  details VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (log_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
