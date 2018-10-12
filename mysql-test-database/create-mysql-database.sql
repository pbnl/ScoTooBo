CREATE USER 'scotoobo'@'localhost' IDENTIFIED BY 'secret';
GRANT ALL PRIVILEGES ON * . * TO 'scotoobo'@'localhost';
CREATE DATABASE scotoobo;
FLUSH PRIVILEGES;
USE scotoobo
