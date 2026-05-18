-- Run this once to create the users table required by signup/login
-- Adjust DB name/user as needed.

-- NOTE:
-- Your app uses roles(roleId) from init_roles_db.sql (admin=1, broker=2, seller=3).

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  firstname VARCHAR(100) NOT NULL,
  lastname VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  phonenumber VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  roleId INT NOT NULL DEFAULT 2,

  CONSTRAINT fk_users_role FOREIGN KEY (roleId) REFERENCES roles(roleId)
);

