-- Run this once to create the users table required by signup/login
-- Adjust DB name/user as needed.

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  roleId INT NOT NULL DEFAULT 2
);

-- Roles mapping (per your request)
-- roleId = 1  -> admin
-- roleId = 2  -> broker
-- roleId = 3  -> seller
--
-- Default roleId for new signups in this app (php/signup.php) is 2.
-- Optional: sample seed user
-- NOTE: This app stores plain text passwords currently (as seen in php/login.php).
-- For production, always hash passwords.
-- INSERT INTO users (username, password, roleId) VALUES ('admin', 'admin123', 1);


