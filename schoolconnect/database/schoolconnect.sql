-- ============================================================
-- SchoolConnect Database Schema
-- Compatible with phpMyAdmin / XAMPP MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS schoolconnect
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE schoolconnect;

-- ── ADMIN USERS ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS admin_users (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  username     VARCHAR(80)  NOT NULL UNIQUE,
  password     VARCHAR(255) NOT NULL,          -- bcrypt hash
  full_name    VARCHAR(120) NOT NULL,
  email        VARCHAR(120) NOT NULL,
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin  (password: Admin@1234)
INSERT INTO admin_users (username, password, full_name, email) VALUES
('admin', '$2y$12$6T1q8K2z.H5VvJmOkN0wsuZ9hKfzP3Lr1wQKqT6TvYb3IZq6Xk8Q2',
 'School Administrator', 'admin@schoolconnect.local');

-- ── PARENTS ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS parents (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  first_name      VARCHAR(80)  NOT NULL,
  last_name       VARCHAR(80)  NOT NULL,
  phone           VARCHAR(30)  NOT NULL,
  email           VARCHAR(120),
  address         TEXT,
  relationship    ENUM('Father','Mother','Guardian','Other') NOT NULL DEFAULT 'Guardian',
  photo_path      VARCHAR(255),
  registered_at   DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── STUDENTS ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS students (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  parent_id       INT NOT NULL,
  first_name      VARCHAR(80)  NOT NULL,
  last_name       VARCHAR(80)  NOT NULL,
  student_class   VARCHAR(60)  NOT NULL,
  date_of_birth   DATE,
  gender          ENUM('Male','Female','Other'),
  student_id_no   VARCHAR(60),
  photo_path      VARCHAR(255),
  registered_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_student_parent FOREIGN KEY (parent_id)
    REFERENCES parents(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── EVENTS ───────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS events (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(160) NOT NULL,
  event_type   ENUM('PTA Meeting','Visitation Day','Sports Day','Open Day','Other') NOT NULL DEFAULT 'PTA Meeting',
  event_date   DATE         NOT NULL,
  event_time   TIME,
  venue        VARCHAR(160),
  description  TEXT,
  created_by   INT,
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_event_admin FOREIGN KEY (created_by)
    REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Seed upcoming events
INSERT INTO events (name, event_type, event_date, event_time, venue, description, created_by) VALUES
('End-of-Term PTA Meeting',  'PTA Meeting',    DATE_ADD(CURDATE(), INTERVAL 14 DAY), '10:00:00', 'School Assembly Hall', 'Discuss term results and upcoming calendar.', 1),
('Visitation Day – Term 2',  'Visitation Day', DATE_ADD(CURDATE(), INTERVAL 30 DAY), '09:00:00', 'School Compound',       'Parents may visit their wards in classrooms.', 1),
('Inter-School Sports Day',  'Sports Day',     DATE_ADD(CURDATE(), INTERVAL 45 DAY), '08:00:00', 'Sports Field',          'Annual inter-house sports competition.', 1);

-- ── ATTENDANCE / VISITATIONS ──────────────────────────────────
CREATE TABLE IF NOT EXISTS attendance (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  event_id        INT NOT NULL,
  parent_id       INT NOT NULL,
  visit_type      ENUM('Event Attendance','Visitation','Walk-in') NOT NULL DEFAULT 'Event Attendance',
  signed_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
  notes           TEXT,
  UNIQUE KEY uq_event_parent (event_id, parent_id),
  CONSTRAINT fk_att_event  FOREIGN KEY (event_id)  REFERENCES events(id)  ON DELETE CASCADE,
  CONSTRAINT fk_att_parent FOREIGN KEY (parent_id) REFERENCES parents(id) ON DELETE CASCADE
) ENGINE=InnoDB;
