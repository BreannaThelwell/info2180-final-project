/*!40101 SET NAMES utf8 */;

SET TIME_ZONE = '-05:00';

-- Create the dolphin_crm database
DROP DATABASE IF EXISTS dolphin_crm;
CREATE DATABASE IF NOT EXISTS dolphin_crm;
USE dolphin_crm;

-- USERS INFORMATION

-- Create Users table
CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data into Users table
INSERT INTO Users (firstname, lastname, password, email, role) VALUES
    ('Breanna', 'Thelwell', '$2y$10$dOhVZs8KNZAcsqDK48p2Tuu1hXfMjeW0/RKc6XAczxVZ2zcoKP3Ta', 'admin@project1.com', 'admin'),
    ('Sheri-lee', 'Mills', '$2y$10$.mf15ul/lnyuHBym8en5d.uOaFksgl.xz/xT1ABUrH5Myvfi.kJJe', 'admin@project2.com', 'admin'),
    ('Antawn', 'Edwards', '$2y$10$98C2BSKINH9M5DXGtmMCHeIO1RCUofUr/BF7WGjJBPqGo01SK4NEO', 'admin@project3.com', 'admin'),
    ('Makonnen', 'Solomon', '$2y$10$Ef8GNyXrNlNpcR0vnseWbOVbgmQrEzCvHTeC4kBHsdQwWdQ0QGuc.', 'admin@project4.com', 'admin'),
    ('Gabe', 'Riley', '$2y$10$/ox0gWfr12HtD9PKZyxYgehSywFSGEZX0gCoZd5I9WoThliWvCy3S', 'admin@project5.com', 'admin');

-- CONTACT INFORMATION

-- Create Contacts table
CREATE TABLE Contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(50) NOT NULL DEFAULT '',
    firstname VARCHAR(50) NOT NULL DEFAULT '',
    lastname VARCHAR(50) NOT NULL DEFAULT '',
    email VARCHAR(150) NOT NULL DEFAULT '',
    telephone VARCHAR(15) NOT NULL DEFAULT '',
    company VARCHAR(50),
    type ENUM('Support', 'Sales Leads') NOT NULL DEFAULT 'Support',
    assigned_to INT DEFAULT NULL,
    created_by INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES Users (id),
    FOREIGN KEY (created_by) REFERENCES Users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- NOTES INFORMATION

-- Create Notes table
CREATE TABLE Notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contact_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES Contacts (id),
    FOREIGN KEY (created_by) REFERENCES Users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data into Notes table
INSERT INTO Notes (contact_id, created_by, comment) VALUES
    (1, 3, 'Customer Added Successfully'),
    (2, 4, 'Customer Not Added');

-- ADDITIONAL STATEMENTS

-- Create a new database user
CREATE USER 'adminDolphinCRM'@'localhost' IDENTIFIED BY 'password123';

-- Grant privileges to the new user
GRANT ALL PRIVILEGES ON dolphin_crm.* TO 'adminDolphinCRM'@'localhost';
FLUSH PRIVILEGES;

-- Sample Insert for an admin user
-- Example for creating a new admin user with password hashing:
-- echo "INSERT INTO Users (firstname, lastname, password, email, role) VALUES 
-- ('Firstname', 'Lastname', '".password_hash('password123', PASSWORD_DEFAULT)."', 'admin@project6.com', 'admin');";
