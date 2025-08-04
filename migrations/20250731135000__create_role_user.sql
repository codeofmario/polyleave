-- migrate:up
CREATE TABLE role_user
(
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    PRIMARY KEY (user_id, role_id),

    CONSTRAINT fk_ru_user
        FOREIGN KEY (user_id) REFERENCES users (id)
            ON DELETE CASCADE,

    CONSTRAINT fk_ru_role
        FOREIGN KEY (role_id) REFERENCES roles (id)
            ON DELETE CASCADE
);

-- migrate:down
DROP TABLE IF EXISTS role_user;