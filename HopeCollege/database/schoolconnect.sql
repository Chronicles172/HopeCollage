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
  password     VARCHAR(255) NOT NULL,
  full_name    VARCHAR(120) NOT NULL,
  email        VARCHAR(120) NOT NULL,
  role         ENUM('admin','domestic_affairs','houseparent_male','houseparent_female','houseparent') NOT NULL DEFAULT 'admin',
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Admin (password: Admin@1234)
INSERT INTO admin_users (username, password, full_name, email, role) VALUES
('admin', '$2y$12$f4EOQLX6nYpIEZcxC.aBiO9XK0ZiAmHaFq4SGFOPGiKCqOC6UGCLm', 'School Administrator', 'admin@schoolconnect.local', 'admin');

-- Head of Domestic Affairs (password: Domestic@1234)
INSERT INTO admin_users (username, password, full_name, email, role) VALUES
('domestic_affairs', '$2y$12$baOnF5GWjOWEXie0RRHjuuKoFfCQaYbx454ZQLHAdfLJcYld/glZy', 'Head of Domestic Affairs', 'domestic@schoolconnect.local', 'domestic_affairs');

-- House Parent (password: HouseParent@1234) - single login, gender chosen after login
INSERT INTO admin_users (username, password, full_name, email, role) VALUES
('houseparent', '$2y$12$1Mc7sby8Jj96WvdiyVK.cuXxRsjX1dBiB2c03NX0dfWw87DMgdYyS', 'House Parent', 'hp@schoolconnect.local', 'houseparent');

-- ── PARENTS ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS parents (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  first_name      VARCHAR(80)  NOT NULL,
  last_name       VARCHAR(80)  NOT NULL,
  phone           VARCHAR(30)  NOT NULL,
  email           VARCHAR(120),
  address         TEXT,
  relationship    ENUM('Father','Mother','Guardian','Other') NOT NULL DEFAULT 'Guardian',
  national_id_type ENUM('Ghana Card','Passport','Driver''s License'),
  national_id_no  VARCHAR(80),
  photo_path      VARCHAR(255),
  registered_at   DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── STUDENTS ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS students (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  parent_id       INT NOT NULL,          -- primary/registering parent (kept for backwards compat)
  first_name      VARCHAR(80)  NOT NULL,
  last_name       VARCHAR(80)  NOT NULL,
  student_class   VARCHAR(60)  NOT NULL,
  house           VARCHAR(80),
  nhis_id         VARCHAR(60),
  date_of_birth   DATE,
  gender          ENUM('Male','Female','Other'),
  student_id_no   VARCHAR(60),
  photo_path      VARCHAR(255),
  registered_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  is_linked_copy  TINYINT(1) NOT NULL DEFAULT 0,
  source_student_id INT NULL DEFAULT NULL,
  CONSTRAINT fk_student_parent FOREIGN KEY (parent_id)
    REFERENCES parents(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── STUDENT ↔ PARENT (many-to-many) ──────────────────────────
-- Each student can have multiple parents/guardians.
-- This replaces the old "is_linked_copy" hack.
CREATE TABLE IF NOT EXISTS student_parents (
  student_id  INT NOT NULL,
  parent_id   INT NOT NULL,
  PRIMARY KEY (student_id, parent_id),
  CONSTRAINT fk_sp_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  CONSTRAINT fk_sp_parent  FOREIGN KEY (parent_id)  REFERENCES parents(id)  ON DELETE CASCADE
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

INSERT INTO events (name, event_type, event_date, event_time, venue, description, created_by) VALUES
('End-of-Term PTA Meeting',  'PTA Meeting',    DATE_ADD(CURDATE(), INTERVAL 14 DAY), '10:00:00', 'School Assembly Hall', 'Discuss term results and upcoming calendar.', 1),
('Visitation Day - Term 2',  'Visitation Day', DATE_ADD(CURDATE(), INTERVAL 30 DAY), '09:00:00', 'School Compound',      'Parents may visit their wards in classrooms.', 1),
('Inter-School Sports Day',  'Sports Day',     DATE_ADD(CURDATE(), INTERVAL 45 DAY), '08:00:00', 'Sports Field',         'Annual inter-house sports competition.', 1);

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

-- ── EXEAT REQUESTS ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS exeat_requests (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  student_id      INT NOT NULL,
  parent_id       INT NOT NULL,
  reason          TEXT NOT NULL,
  departure_date  DATE NOT NULL,
  departure_time  TIME NOT NULL,
  expected_return DATE NOT NULL,
  actual_return   DATE,
  status          ENUM('pending','approved','declined') NOT NULL DEFAULT 'pending',
  review_note     TEXT,
  reviewed_by     INT,
  reviewed_at     DATETIME,
  created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_exeat_student  FOREIGN KEY (student_id)  REFERENCES students(id)  ON DELETE CASCADE,
  CONSTRAINT fk_exeat_parent   FOREIGN KEY (parent_id)   REFERENCES parents(id)   ON DELETE CASCADE,
  CONSTRAINT fk_exeat_reviewer FOREIGN KEY (reviewed_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── Unified houseparent role (added for single-login gender-picker flow) ──
-- Run these ALTER + INSERT statements on an existing database:
-- ALTER TABLE admin_users MODIFY COLUMN role ENUM('admin','domestic_affairs','houseparent_male','houseparent_female','houseparent') NOT NULL DEFAULT 'admin';
-- INSERT INTO admin_users (username, password, full_name, email, role) VALUES
-- ('houseparent', '$2y$12$1Mc7sby8Jj96WvdiyVK.cuXxRsjX1dBiB2c03NX0dfWw87DMgdYyS', 'House Parent', 'hp@schoolconnect.local', 'houseparent');
