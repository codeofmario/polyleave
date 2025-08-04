-- migrate:up

-- 1) Roles ----------------------------------------------------------
INSERT
IGNORE INTO roles (id, name) VALUES
  (1, 'user'),
  (2, 'moderator'),
  (3, 'admin');

-- 2) Organisational Units ------------------------------------------
INSERT
IGNORE INTO units (id, name) VALUES
  (1, 'Engineering'),
  (2, 'HR');

-- 3) Demo moderator account ----------------------------------------
-- bcrypt('secret')
INSERT INTO users
(id, unit_id, name, email, password, created_at)
VALUES (100, 1, 'Mod', 'moderator@example.com',
        '$2y$12$HbIaVVm.9VwcwosqDhmCCe0vLAHKMZG9nOTIJj2dvq0srfmwR.vHu', NOW()) ON DUPLICATE KEY
UPDATE email = email; -- safe for re-runs

-- Attach role “moderator”
INSERT
IGNORE INTO role_user (user_id, role_id) VALUES (100, 2);

-- 4) Demo ordinary user account ------------------------------------
-- bcrypt('secret')
INSERT INTO users
(id, unit_id, name, email, password, created_at)
VALUES (101, 2, 'John', 'john@example.com',
        '$2y$12$HbIaVVm.9VwcwosqDhmCCe0vLAHKMZG9nOTIJj2dvq0srfmwR.vHu', NOW()) ON DUPLICATE KEY
UPDATE email = email; -- safe for re-runs

-- Attach role “user”
INSERT
IGNORE INTO role_user (user_id, role_id) VALUES (101, 1);

-- 5) Sample approved leave (shows up in dashboard) -----------------
INSERT
IGNORE INTO leave_requests
  (user_id, start_date,                     end_date,                       reason,     status,    created_at)
VALUES
  (100,     CURDATE() + INTERVAL 14 DAY,    CURDATE() + INTERVAL 18 DAY,    'Conference','approved', NOW());

-- migrate:down
DELETE
FROM leave_requests
WHERE user_id = 100
  AND reason = 'Conference';

-- Ordinary user rollback
DELETE
FROM role_user
WHERE user_id = 101
  AND role_id = 1;
DELETE
FROM users
WHERE id = 101;

-- Moderator rollback
DELETE
FROM role_user
WHERE user_id = 100
  AND role_id = 2;
DELETE
FROM users
WHERE id = 100;

DELETE
FROM units
WHERE id IN (1, 2);
DELETE
FROM roles
WHERE id IN (1, 2, 3);
