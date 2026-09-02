-- ============================================================
-- SchoolConnect Migration v3
-- Fixes parent-ward relationship with a proper junction table.
-- Run this ONCE on your existing database.
-- ============================================================

USE schoolconnect;

-- ── 1. Create the junction table ─────────────────────────────
CREATE TABLE IF NOT EXISTS student_parents (
  student_id  INT NOT NULL,
  parent_id   INT NOT NULL,
  PRIMARY KEY (student_id, parent_id),
  CONSTRAINT fk_sp_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  CONSTRAINT fk_sp_parent  FOREIGN KEY (parent_id)  REFERENCES parents(id)  ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── 2. Migrate existing data ──────────────────────────────────
-- Copy every real (non-linked-copy) student → parent row into the junction table
INSERT IGNORE INTO student_parents (student_id, parent_id)
SELECT id, parent_id
FROM students
WHERE is_linked_copy = 0;

-- Copy every linked-copy row: the ORIGINAL student links to the COPY's parent
INSERT IGNORE INTO student_parents (student_id, parent_id)
SELECT source_student_id, parent_id
FROM students
WHERE is_linked_copy = 1
  AND source_student_id IS NOT NULL;

-- ── 3. Remove linked-copy rows from students ──────────────────
-- They are no longer needed now that the junction table holds the relationship
DELETE FROM students WHERE is_linked_copy = 1;

-- ── 4. Optional: remove the now-redundant helper columns ──────
-- (leave them for now; they are harmless and make rollback easier)
-- ALTER TABLE students DROP COLUMN is_linked_copy;
-- ALTER TABLE students DROP COLUMN source_student_id;

-- Done!
SELECT 'Migration v3 complete.' AS status;
