-- Create the database
CREATE store_management;
USE store_management;

CREATE TABLE users (
    id int(11) AI PK 
    username varchar(50) 
    password varchar(255) 
    role enum('Admin','StoreManager','Requester','Viewer','Branch','Buyer') 
    email varchar(100) 
    gender enum('Male','Female','Other') 
    birthdate date 
    phone varchar(20)
);

-- Insert Sample Users
INSERT INTO users (username, password, role) VALUES 
('admin1', '0922', 'Admin'),
('manager1', '0930', 'StoreManager'),
('requester1', '0940', 'Requester'),
('viewer1', '0950', 'Viewer'),
('branch1', '0960', 'Branch')
('buyer1', '0970','Buyer');


CREATE TABLE branch_inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    branch_name VARCHAR(255),
    item_name VARCHAR(255),
    quantity INT,
    unit_price FLOAT,
    supplier VARCHAR(255),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE branch_request (
id int(11) AI PK,
item_name varchar(255),
quantity int(11) ,
price decimal(10,2) ,
status enum('Pending','Approved','Rejected'),
timestamp timestamp 
requester varchar(50));

-- Inventory Table
CREATE TABLE  inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    supplier VARCHAR(100) NOT NULL
);

-- Item Requests Table
CREATE TABLE item_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requester VARCHAR(50) NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected', 'Received') DEFAULT 'Pending',
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Item Suggestions Table (from Viewer)
CREATE TABLE item_suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    viewer VARCHAR(50) NOT NULL,
    item_name VARCHAR(100) NOT NULL,
    reason TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Activity Log Table
CREATE TABLE activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    activity TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
);
