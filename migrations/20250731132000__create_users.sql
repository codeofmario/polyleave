-- migrate:up
CREATE TABLE users
(
    id             INT AUTO_INCREMENT PRIMARY KEY,
    unit_id        INT,
    name           VARCHAR(100) NOT NULL,
    email          VARCHAR(150) NOT NULL UNIQUE,
    password       VARCHAR(255),
    provider       VARCHAR(50),
    provider_id    VARCHAR(191),
    provider_token TEXT,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_unit FOREIGN KEY (unit_id) REFERENCES units (id)
);

-- migrate:down
DROP TABLE IF EXISTS users;
