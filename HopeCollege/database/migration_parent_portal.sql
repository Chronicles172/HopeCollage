-- ============================================================
-- SchoolConnect: Parent Portal Migration
-- Run after schoolconnect.sql
-- ============================================================

USE schoolconnect;

-- ── ANNOUNCEMENTS ─────────────────────────────────────────────
-- Admin posts announcements; all parents can view them in
-- their portals.
CREATE TABLE IF NOT EXISTS announcements (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  title        VARCHAR(200) NOT NULL,
  body         TEXT         NOT NULL,
  is_pinned    TINYINT(1)   NOT NULL DEFAULT 0,
  created_by   INT,
  created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ann_admin FOREIGN KEY (created_by)
    REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Seed a welcome announcement
INSERT INTO announcements (title, body, is_pinned, created_by) VALUES
(
  'Welcome to the SchoolConnect Parent Portal',
  'Dear Parents and Guardians,\n\nWelcome to the SchoolConnect Parent Portal! You can now log in using your registered phone number to:\n\n• View upcoming school events\n• Track your exeat requests (pending, approved, or declined)\n• Read important announcements from the school\n\nIf you have any questions, please contact the school administration.',
  1,
  1
);
