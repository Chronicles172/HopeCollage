-- ══════════════════════════════════════════════════════════════════
--  Migration: Replace houseparent_male & houseparent_female
--             with a single 'houseparent' account
--
--  Run this on your EXISTING database (if already set up).
--  If you are doing a fresh install, just import schoolconnect.sql
--  — this migration is already included there.
--
--  Login after migration:
--    Username : houseparent
--    Password : HouseParent@1234
-- ══════════════════════════════════════════════════════════════════

-- 1. Add the new role value
ALTER TABLE admin_users
  MODIFY COLUMN role
    ENUM('admin','domestic_affairs','houseparent_male','houseparent_female','houseparent')
    NOT NULL DEFAULT 'admin';

-- 2. Remove the old separate accounts
DELETE FROM admin_users WHERE username IN ('houseparent_male','houseparent_female');

-- 3. Insert the single shared account (password: HouseParent@1234)
INSERT INTO admin_users (username, password, full_name, email, role)
VALUES (
  'houseparent',
  '$2y$12$1Mc7sby8Jj96WvdiyVK.cuXxRsjX1dBiB2c03NX0dfWw87DMgdYyS',
  'House Parent',
  'hp@schoolconnect.local',
  'houseparent'
);
