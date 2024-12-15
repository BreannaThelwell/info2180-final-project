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
    id INT(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL default '',
    lastname VARCHAR(50) NOT NULL default '',
    password VARCHAR(50) NOT NULL default '',
    email VARCHAR(150) NOT NULL UNIQUE default '',
    role VARCHAR(50) NOT NULL default '',
    created_at DATETIME default NOW(),
    PRIMARY KEY (id)
)ENGINE = MYISAM AUTOINCREMENT = 1 DEFAULT CHARSET = utf8mb4;


-- Users Insert Data
LOCK TABLES users WRITE;
INSERT INTO users(firstname, lastname, password, email, role) VALUES
    ('Breanna', 'Thelwell', password_hash('password123', PASSWORD_DEFAULT), 'admin@project2.com', 'admin'),
    ('Sheri-lee', 'Mills', password_hash('password123', PASSWORD_DEFAULT), 'admin@project2.com', 'admin1'),
    ('Antawn', 'Edwards', password_hash('password123', PASSWORD_DEFAULT), 'admin@project2.com', 'admin3'),
    ('Makonnen','Solomon', password_hash('password123', PASSWORD_DEFAULT), 'admin@project2.com', 'admin4'),
    ('Gabe', 'Riley', password_hash('password123', PASSWORD_DEFAULT), 'admin@project2.com', 'admin5');
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