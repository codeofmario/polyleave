-- migrate:up
CREATE TABLE roles
(
    id   INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO roles (name)
VALUES ('user'),
       ('moderator');

-- migrate:down
DROP TABLE IF EXISTS roles;