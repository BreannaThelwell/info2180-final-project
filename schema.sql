/*sql script for creating database:schema.sql*/

/*!40101 SET NAMES utf8 */;

SET TIME_ZONE = '-05:00';

-- Create the dolphin_crm database
DROP DATABASE IF EXISTS dolphin_crm;
CREATE DATABASE IF NOT EXISTS dolphin_crm;
USE dolphin_crm;

-- USERS INFORMATION

-- Create Users table
 CREATE TABLE Users (
    id INT(5) AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 



-- Users Insert Data
LOCK TABLES users WRITE;
INSERT INTO users(firstname, lastname, password, email, role) VALUES
    ('Breanna', 'Thelwell', '$2y$10$dOhVZs8KNZAcsqDK48p2Tuu1hXfMjeW0/RKc6XAczxVZ2zcoKP3Ta', 'admin@project2.com', 'admin'),
    ('Sheri-lee', 'Mills', '$2y$10$.mf15ul/lnyuHBym8en5d.uOaFksgl.xz/xT1ABUrH5Myvfi.kJJe', 'admin@project2.com', 'admin1'),
    ('Antawn', 'Edwards', '$2y$10$98C2BSKINH9M5DXGtmMCHeIO1RCUofUr/BF7WGjJBPqGo01SK4NEO', 'admin@project2.com', 'admin3'),
    ('Makonnen','Solomon', '$2y$10$Ef8GNyXrNlNpcR0vnseWbOVbgmQrEzCvHTeC4kBHsdQwWdQ0QGuc.', 'admin@project2.com', 'admin4'),
    ('Gabe', 'Riley', '$2y$10$/ox0gWfr12HtD9PKZyxYgehSywFSGEZX0gCoZd5I9WoThliWvCy3S', 'admin@project2.com', 'admin5');
UNLOCK TABLES; 

--CONTACT INFORMATION

-- Create Contacts table
CREATE TABLE Contacts (
    id INT(3) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id),
    title VARCHAR(50) NOT NULL default'',
    firstname VARCHAR(50) NOT NULL default'',
    lastname VARCHAR(50) NOT NULL default'',
    email VARCHAR(150) NOT NULL default'',
    telephone VARCHAR(15) NOT NULL default'',
    company VARCHAR(50),
    type enum('Support', 'Sale Leads') NOT NULL default 'Support',
    assigned_to INT(10) default NULL,
    created_by INT(10) default NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES Users(id),
    FOREIGN KEY (created_by) REFERENCES Users(id)
)ENGINE = MYISAM AUTOINCREMENT = 1 DEFAULT CHARSET = utf8mb4;

-- NOTES INFORMATION

-- Create Notes table
CREATE TABLE Notes (
    id INT(5) AUTO_INCREMENT PRIMARY KEY,
    contact_id(3) INT NOT NULL,
    comment TEXT NOT NULL,
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES Contacts(id),
    FOREIGN KEY (created_by) REFERENCES Users(id)
)ENGINE = MYISAM AUTOINCREMENT = 1 DEFAULT CHARSET = utf8mb4;

--Notes Insert Data
LOCK TABLES notes WRITE;
INSERT INTO notes(contact_id, created_by, comment) VALUES
    ('1', 3, 'Customer Added Successfully'),
    ('2', 4, 'Customer Not Added');
UNLOCK TABLES;

/*
ADDITIONAL STATEMENTS

Create a new user
CREATE USER 'adminDolphinCRM'@'localhost' IDENTIFIED BY 'password123';

User Privileges
GRANT ALL PRIVILEGES ON dolphin_crm. * TO 'adminCRM'@'localhost';
FLUSH PRIVILEGES;

*/
-- Insert admin user
/*INSERT INTO Users (firstname, lastname, password, email, role)
echo "INSERT INTO users(firstname, lastname, password, email, role) VALUES ('firstname', 'lastname', '".password_hash('password123', PASSWORD_DEFAULT)."', 'admin@project2.com', 'admin');";*/