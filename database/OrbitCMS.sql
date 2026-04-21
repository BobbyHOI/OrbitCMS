SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- 1. Roles
CREATE TABLE `tblroles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `RoleName` varchar(100) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tblroles` (id, RoleName, Description) VALUES
(1, 'Employee', 'Standard staff access'),
(2, 'Manager', 'Departmental approval authority'),
(3, 'Admin', 'Full system control');

-- 2. System Settings
CREATE TABLE `tblsettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `SettingKey` varchar(100) NOT NULL,
  `SettingValue` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `SettingKey` (`SettingKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tblsettings` (SettingKey, SettingValue) VALUES
('base_max_leaves', '20'),
('working_days', '1,2,3,4,5'),
('company_name', 'OrbitCMS'),
('company_country', 'Nigeria'),
('company_country_code', 'NG'),
('admin_email', 'admin@orbitcms.com'),
('manager_emails', 'manager@orbitcms.com'),
('cc_emails', '');

-- 3. Departments
CREATE TABLE `tbldepartments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `DepartmentName` varchar(150) NOT NULL,
  `DepartmentShortName` varchar(100) NOT NULL,
  `DepartmentCode` varchar(50) DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tbldepartments` (DepartmentName, DepartmentShortName, DepartmentCode) VALUES
('Human Resource', 'HR', 'HR001'),
('Information Technology', 'IT', 'IT001'),
('Operations', 'OP', 'OP001');

-- 4. Employees
CREATE TABLE `tblemployees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `EmpId` varchar(100) NOT NULL,
  `FirstName` varchar(150) NOT NULL,
  `LastName` varchar(150) NOT NULL,
  `EmailId` varchar(200) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Gender` varchar(100) DEFAULT NULL,
  `Dob` varchar(100) DEFAULT NULL,
  `Department` varchar(255) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(200) DEFAULT NULL,
  `State` varchar(150) DEFAULT NULL,
  `Country` varchar(150) DEFAULT 'Nigeria',
  `Phonenumber` char(15) DEFAULT NULL,
  `Status` int(1) NOT NULL DEFAULT 1,
  `role` int(11) NOT NULL,
  `LeavesTaken` int(11) DEFAULT 0,
  `ExtraLeaves` int(11) DEFAULT 0,
  `imageFileName` varchar(255) DEFAULT 'profile-image.png',
  `hash` varchar(255) DEFAULT NULL,
  `RegDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `EmailId` (`EmailId`),
  UNIQUE KEY `EmpId` (`EmpId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Demo Users (Passwords synced via setup.php)
INSERT INTO `tblemployees` (id, EmpId, FirstName, LastName, EmailId, Password, Gender, Dob, Department, role, LeavesTaken, hash, Country) VALUES
(1, 'ADM001', 'System', 'Admin', 'admin@orbitcms.com', '---', 'Male', '1990-01-01', 'Information Technology', 3, 0, 'hash123', 'Nigeria'),
(2, 'MGR001', 'Alice', 'Manager', 'manager@orbitcms.com', '---', 'Female', '1985-05-15', 'Human Resource', 2, 0, 'hash456', 'Nigeria'),
(3, 'EMP001', 'John', 'Doe', 'employee@orbitcms.com', '---', 'Male', '1995-10-10', 'Operations', 1, 3, 'hash789', 'Nigeria');

-- 5. Leave Types
CREATE TABLE `tblleavetype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `LeaveType` varchar(200) NOT NULL,
  `Description` mediumtext,
  `LeaveLimit` int(11) DEFAULT 0,
  `CreationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tblleavetype` (LeaveType, Description, LeaveLimit) VALUES
('Annual Leave', 'Standard yearly holiday quota', 20),
('Medical Leave', 'Sick leave (unlimited)', 0),
('Casual Leave', 'Short term personal leave', 10);

-- 6. Leaves
CREATE TABLE `tblleaves` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `LeaveType` varchar(110) NOT NULL,
  `FromDate` date NOT NULL,
  `ToDate` date NOT NULL,
  `DaysRequested` int(11) DEFAULT 0,
  `Description` mediumtext NOT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `AdminRemark` mediumtext,
  `AdminRemarkDate` varchar(120) DEFAULT NULL,
  `Status` int(1) NOT NULL DEFAULT 0,
  `IsRead` int(1) NOT NULL DEFAULT 0,
  `empid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample Leaves for John Doe (ID: 3)
INSERT INTO `tblleaves` (LeaveType, FromDate, ToDate, DaysRequested, Description, Status, empid, AdminRemark) VALUES
('Annual Leave', '2024-01-10', '2024-01-12', 3, 'Family vacation', 1, 3, 'Enjoy your break!'),
('Medical Leave', '2024-02-15', '2024-02-15', 1, 'Dental checkup', 0, 3, NULL),
('Casual Leave', '2024-03-01', '2024-03-02', 2, 'Personal errands', 2, 3, 'Not possible during peak season.');

-- 7. Holidays
CREATE TABLE `tblholidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `HolidayName` varchar(150) NOT NULL,
  `HolidayDate` date NOT NULL,
  `CreationDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `tblholidays` (HolidayName, HolidayDate) VALUES
('New Year', '2024-01-01'),
('Christmas', '2024-12-25');

COMMIT;