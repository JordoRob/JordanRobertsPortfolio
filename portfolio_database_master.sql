#LABOUR BOARD DATABASE
CREATE DATABASE Schedule;
USE Schedule;

CREATE TABLE user (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(64),
  password VARCHAR(100),
  recent_password_reset BOOLEAN DEFAULT true,
  is_admin BOOLEAN DEFAULT false,
  custom_order MEDIUMTEXT DEFAULT NULL,
  use_background BOOLEAN DEFAULT false,
  refresh_timer INT NOT NULL DEFAULT 30,
  created_at TIMESTAMP
);

CREATE TABLE job (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  address VARCHAR(255) NULL DEFAULT NULL,
  archived DATE NULL DEFAULT NULL,
  manager_name VARCHAR(255) NULL DEFAULT NULL,
  start_date DATE NULL DEFAULT NULL,
  end_date DATE NULL DEFAULT NULL,
  notes TEXT
);


CREATE TABLE employee (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role INT, /*0:Foreman 1:Journeyman 2:? Years ascending */
  name VARCHAR(100),
  active INT DEFAULT 0,
  archived DATE DEFAULT NULL,
  img VARCHAR(100) DEFAULT "img/emp/default.png",
  birthday DATE DEFAULT NULL,
  phoneNum VARCHAR(15) DEFAULT "(000) 000-0000",
  phoneNumSecondary VARCHAR(15) DEFAULT "(000) 000-0000",
  notes TEXT,
  email VARCHAR(200) DEFAULT "",
  hired DATE DEFAULT NULL,
  redseal INT DEFAULT 0
);

CREATE TABLE worksOn (
  employee_id INT,
  job_id INT,
  FOREIGN KEY (employee_id) REFERENCES employee(id) ON DELETE CASCADE,
  FOREIGN KEY (job_id) REFERENCES job(id) ON DELETE CASCADE,
  UNIQUE (employee_id, job_id)
);

CREATE TABLE outlook (
  job_id INT,
  date DATE,
  count INT,
  PRIMARY KEY (job_id, date),
  FOREIGN KEY (job_id) REFERENCES job(id) ON DELETE CASCADE
);

CREATE TABLE assignments (
  assignment_id INT AUTO_INCREMENT PRIMARY KEY,
  employee_id INT,
  job_id INT,
  start_date DATE,
  end_date DATE,
  assigner INT,
  FOREIGN KEY (employee_id) REFERENCES employee(id) ON DELETE CASCADE,
  FOREIGN KEY (job_id) REFERENCES job(id) ON DELETE CASCADE,
  FOREIGN KEY (assigner) REFERENCES user(id) ON DELETE CASCADE
);

ALTER TABLE `worksOn` ADD PRIMARY KEY( `employee_id`, `job_id`); 

CREATE TABLE update_time (
  table_name VARCHAR(50) PRIMARY KEY,
  last_update TIMESTAMP
);

-- Triggers for last_update column in update_time table

CREATE TRIGGER `emp_update` AFTER UPDATE ON `employee` 
FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'employee'; 

CREATE TRIGGER `emp_insert` AFTER INSERT ON `employee` 
FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'employee'; 

CREATE TRIGGER `job_update` AFTER UPDATE ON `job` 
FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'job'; 

CREATE TRIGGER `job_insert` AFTER INSERT ON `job` 
FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'job'; 

CREATE TRIGGER `worksOn_update` AFTER UPDATE ON `worksOn` 
FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'worksOn';

CREATE TRIGGER `worksOn_insert` AFTER INSERT ON `worksOn` 
FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'worksOn';

CREATE TRIGGER `outlook_insert` AFTER INSERT ON `outlook` 
FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'outlook';

CREATE TRIGGER `outlook_update` AFTER UPDATE ON `outlook` 
FOR EACH ROW UPDATE update_time 
SET last_update=CURRENT_TIMESTAMP() 
WHERE table_name LIKE 'outlook';

CREATE TRIGGER `custom_order_update` AFTER UPDATE ON `user`
FOR EACH ROW UPDATE update_time
SET last_update=CURRENT_TIMESTAMP()
WHERE table_name LIKE 'user';

-- Trigger to set active status to -1 if archived is not null (insert trigger used when inserting archived employees from test data or loading backups, update trigger used when change_active() is called when a user archives an employee). This allows us to have employees that have an archived date but are not archived (in case they want to restore the old archive date)

DELIMITER $$
CREATE TRIGGER set_active_before_insert
BEFORE INSERT ON employee
FOR EACH ROW
BEGIN
    IF NEW.archived IS NOT NULL THEN
        SET NEW.active = -1;
    END IF;
END$$

CREATE TRIGGER set_active_before_update
BEFORE UPDATE ON employee
FOR EACH ROW
BEGIN
    IF NEW.active = -1 AND OLD.archived IS NULL THEN
        SET NEW.archived = CURRENT_DATE();
    END IF;
END$$
DELIMITER ;

-- Trigger end

FLUSH TABLES;

#POPULATE LABOUR BOARD

USE Schedule;

-- Insert static users
INSERT INTO user (id, username, password, recent_password_reset, is_admin, created_at)
VALUES 
(1, 'admin', md5('adminpassword'), false, 1, NOW()),
(2, "projector", md5("projector_password"), false, 2, NOW());

-- Insert sample users
INSERT INTO user (username, password, recent_password_reset, is_admin, created_at)
VALUES 
('johnsmith', md5('password123'), true, false, NOW()),
('testadmin', md5('adminpassword'), false, true, NOW()),
('testuser1', md5('password'), false, false, NOW()),
('testuser2', md5('password'), false, false, NOW()),
('testuser3', md5('password'), false, false, NOW()),
('portfolioUser', md5('Portfolio123'), false, false, NOW());


INSERT INTO employee (role, name, active, archived, birthday, hired, phoneNum)
VALUES
(0, 'John Smith', 0, NULL, DATE '1975-06-12', DATE '2010-03-15', '(123) 555-1234'),
(1, 'Michael Johnson', 0, NULL, DATE '1982-09-25', DATE '2015-11-02', '(456) 555-5678'),
(2, 'David Williams', 0, NULL, DATE '1988-02-07', DATE '2018-07-10', '(789) 555-9876'),
(2, 'James Brown', 0, NULL, DATE '1991-11-18', DATE '2016-05-20', '(012) 555-2468'),
(1, 'Sarah Davis', 1, NULL, DATE '1980-05-03', NULL, '(345) 555-7890'),
(2, 'Emily Wilson', 0, NULL, DATE '1985-08-22', DATE '2019-09-05', '(678) 555-1357'),
(3, 'Daniel Anderson', 0, NULL, DATE '1993-03-31', DATE '2020-02-18', '(901) 555-8024'),
(4, 'Jessica Martinez', 0, NULL, DATE '1979-07-16', DATE '2017-08-12', '(234) 555-3690'),
(5, 'Christopher Taylor', 0, NULL, DATE '1987-12-09', DATE '2014-12-01', '(567) 555-6743'),
(0, 'Matthew Clark', -1, '2023-01-01', DATE '1972-04-28', DATE '2008-09-30', '(890) 555-2468'),
(1, 'Jennifer Rodriguez', 0, NULL, DATE '1995-08-10', DATE '2021-03-05', '(123) 555-9876'),
(2, 'Robert Hernandez', 0, NULL, DATE '1984-02-15', DATE '2019-11-20', '(456) 555-2345'),
(4, 'Karen Lee', 0, NULL, DATE '1990-05-21', DATE '2017-07-10', '(789) 555-7890'),
(1, 'Joshua Thomas', 0, NULL, DATE '1989-09-08', DATE '2014-06-15', '(012) 555-3456'),
(3, 'Michelle Scott', 0, NULL, DATE '1983-03-12', DATE '2012-09-30', '(345) 555-6789'),
(0, 'Andrew Green', 0, NULL, DATE '1977-07-29', DATE '2011-04-18', '(678) 555-9012'),
(1, 'Emily Baker', 0, NULL, DATE '1992-12-01', DATE '2018-08-22', '(901) 555-3456'),
(2, 'David Reed', 0, NULL, DATE '1986-06-19', DATE '2020-02-05', '(234) 555-7890'),
(4, 'Amy Turner', 0, NULL, DATE '1994-01-07', DATE '2016-09-10', '(567) 555-1234'),
(0, 'William Cooper', 0, NULL, DATE '1981-04-25', DATE '2013-07-28', '(890) 555-6789'),
(1, 'Olivia Morgan', 0, NULL, DATE '1988-10-11', DATE '2017-03-15', '(432) 555-9012'),
(1, 'Daniel Allen', 0, NULL, DATE '1991-11-27', DATE '2019-01-20', '(765) 555-2345'),
(1, 'Sophia Ward', 0, NULL, DATE '1996-05-18', DATE '2021-08-05', '(321) 555-5678'),
(2, 'Matthew Evans', 0, NULL, DATE '1987-09-03', DATE '2015-06-10', '(654) 555-7890'),
(1, 'Ava Turner', 0, NULL, DATE '1993-02-22', DATE '2018-09-25', '(987) 555-1234'),
(1, 'Alexander Collins', 0, NULL, DATE '1990-07-14', DATE '2016-05-20', '(876) 555-4567'),
(1, 'Abigail Brooks', 0, NULL, DATE '1985-12-17', DATE '2012-08-12', '(543) 555-7890'),
(0, 'Michael Edwards', 2, NULL, DATE '1979-05-06', DATE '2010-11-01', '(210) 555-1234'),
(1, 'Samantha Morris', 2, NULL, DATE '1994-11-30', DATE '2019-03-05', '(109) 555-4567'),
(2, 'Benjamin Bennett', 0, NULL, DATE '1983-06-27', DATE '2017-09-10', '(876) 555-8901'),
(2, 'Charlotte Gray', 0, NULL, DATE '1989-09-13', DATE '2015-05-15', '(543) 555-2345'),
(3, 'David Hill', 0, NULL, DATE '1992-01-25', DATE '2018-02-20', '(210) 555-5678'),
(1, 'Ella Phillips', 0, NULL, DATE '1996-04-08', DATE '2021-07-10', '(109) 555-8901'),
(0, 'Joseph Turner', 0, NULL, DATE '1976-08-11', DATE '2011-03-05', '(876) 555-2345'),
(2, 'Mia Nelson', 0, NULL, DATE '1993-10-29', DATE '2017-11-20', '(543) 555-5678'),
(3, 'Jacob White', 0, NULL, DATE '1988-03-07', DATE '2013-09-15', '(210) 555-9012'),
(3, 'Avery Russell', 0, NULL, DATE '1991-04-14', DATE '2019-06-10', '(109) 555-2345'),
(2, 'Grace Jenkins', 0, NULL, DATE '1984-11-09', DATE '2015-09-25', '(876) 555-5678'),
(2, 'Daniel Adams', 0, NULL, DATE '1989-12-23', DATE '2016-02-20', '(543) 555-9012'),
(3, 'Sophia Clark', 0, NULL, DATE '1994-03-05', DATE '2020-07-15', '(210) 555-2345'),
(4, 'James Bailey', 0, NULL, DATE '1997-06-16', DATE '2022-01-10', '(109) 555-5678');

-- Insert sample jobs
INSERT INTO job (title, archived, manager_name, start_date, end_date)
VALUES 
    ("McDonald's Building 311 Vernon St.", NULL, 'John Smith', '2023-01-01', '2024-04-30'),
    ('Starbucks Building 123 Main St.', NULL, 'Jane Doe', '2023-02-01', '2024-05-31'),
    ('Walmart Supercenter Construction Project', NULL, 'Michael Johnson', '2023-03-01', '2024-06-30'),
    ('Bank of America Tower Construction', NULL, 'Emily Williams', '2023-04-01', '2024-07-31'),
    ('Residential Complex on Park Avenue', NULL, 'Robert Brown', '2023-05-01', '2024-08-31'),
    ('School Renovation Project at Smith High', NULL, 'Jennifer Lee', '2023-06-01', '2024-09-30'),
    ('Office Building Construction on 5th Avenue', NULL, 'David Miller', '2023-07-01', '2024-10-31'),
    ('Shopping Mall Expansion Project', NULL, 'Sarah Davis', '2023-08-01', '2024-11-30'),
    ('Hospital Construction at City Medical Center', NULL, 'James Anderson', '2023-09-01', '2024-12-31'),
    ('Resort Construction on Paradise Island', NULL, 'Linda Martinez', '2023-10-01', '2025-01-31'),
    ('Sports Stadium Redevelopment Project', NULL, 'William Wilson', '2023-11-01', '2025-02-28'),
    ('Airport Terminal Expansion at International Airport', NULL, 'Karen Taylor', '2023-12-01', '2025-03-31'),
    ('Convention Center Construction on Ocean Boulevard', NULL, 'Richard Johnson', '2024-01-01', '2025-04-30'),
    ('Apartment Complex Construction on Elm Street', NULL, 'Elizabeth Davis', '2024-02-01', '2025-05-31'),
    ('Highway Bridge Rehabilitation Project', NULL, 'Michael Brown', '2024-03-01', '2025-06-30'),
    ('Theme Park Construction at Adventureland', NULL, 'Jennifer Miller', '2024-04-01', '2025-07-31'),
    ('University Building Renovation at State University', NULL, 'Robert Lee', '2024-05-01', '2025-08-31'),
    ('Retail Store Remodeling on Market Street', NULL, 'Karen Williams', '2024-06-01', '2025-09-30'),
    ('Hotel Construction on Sunset Boulevard', NULL, 'David Davis', '2024-07-01', '2025-10-31'),
    ('Archived Job Test', '2023-04-30', 'Michael Johnson', '2022-03-01', '2023-04-30');


-- Insert sample worksOn relationships
INSERT INTO worksOn (employee_id, job_id)
VALUES 
(1, 1),
(2, 1),
(3, 2),
(4, 2),
(6, 3),
(7, 3),
(8, 3);

-- Insert sample outlook entries
INSERT INTO outlook (job_id, date, count)
VALUES 
-- Job 1
(1, '2023-07-01', 5),
(1, '2023-08-01', 4),
(1, '2023-09-01', 4),
(1, '2023-10-01', 3),
-- Job 2
(2, '2023-06-01', 3),
(2, '2023-07-01', 3),
(2, '2023-08-01', 2),
(2, '2023-09-01', 2),
-- Job 3
(3, '2023-07-01', 4),
(3, '2023-08-01', 3),
(3, '2023-09-01', 3),
(3, '2023-10-01', 2);

-- Insert sample historical assignments
INSERT INTO assignments (employee_id, job_id, start_date, end_date)
VALUES 
-- Employee 1
(1, 1, '2023-01-01', '2023-02-01'),
(1, 1, '2023-03-01', '2023-05-01'),
-- Employee 2
(2, 1, '2023-01-01', '2023-02-01'),
(2, 1, '2023-02-01', '2023-03-01'),
(2, 1, '2023-03-01', '2023-04-01'),
-- Employee 3
(3, 2, '2023-02-01', '2023-03-01'),
(3, 2, '2023-03-01', '2023-04-01'),
(3, 2, '2023-04-01', '2023-05-01'),
-- Additional assignments
(1, 3, '2023-02-01', '2023-03-01'),
(2, 3, '2023-04-01', '2022-05-01'),
(3, 3, '2023-05-01', '2023-06-01'),
(4, 3, '2023-01-01', '2023-05-01');

INSERT INTO update_time (table_name, last_update)
VALUES
('worksOn',null),
('employee',null),
('job',null),
('outlook',null),
('user',null);

#FORUM WEBSITE

CREATE DATABASE masonsroom;
USE masonsroom;

-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-server
-- Generation Time: Dec 20, 2023 at 07:46 PM
-- Server version: 8.2.0
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `masonsroom`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `user_id` int NOT NULL,
  `admin_pass` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`user_id`, `admin_pass`) VALUES
(1, 'masonthegoat!'),
(4, 'liamthegoat!'),
(7, '000812'),
(8, 'jordothegoat!'),
(7, '000812'),
(8, 'jordothegoat!');

-- --------------------------------------------------------

--
-- Table structure for table `analytics`
--


CREATE TABLE `analytics` (
  `id` int NOT NULL,
  `page_url` varchar(150) NOT NULL,
  `entry_time` datetime NOT NULL,
  `exit_time` datetime NOT NULL,
  `ip_address` varchar(30) NOT NULL,
  `country` varchar(50) NOT NULL,
  `operating_system` varchar(20) NOT NULL,
  `browser` varchar(20) NOT NULL,
  `browser_version` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banned`
--

CREATE TABLE `banned` (
  `ban_id` int NOT NULL,
  `reason` text COLLATE utf8mb4_general_ci,
  `user_id` int DEFAULT NULL,
  `admin_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int NOT NULL,
  `topic_id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pinned` tinyint(1) NOT NULL DEFAULT '0',
  `score` double DEFAULT '0',
  `post_title` tinytext COLLATE utf8mb4_general_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `tag` varchar(20) COLLATE utf8mb4_general_ci DEFAULT '[Discussion]'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `topic_id`, `user_id`, `content`, `created_at`, `pinned`, `score`, `post_title`, `deleted`, `tag`) VALUES
(1, 11, 2, 'Hey everyone! So i visited the local aquarium the other day and during the live show I picked up one of those rain overcoats. They work surprisingly well to stop your clothes from getting covered in oil! Try this out for yourself and let me know what you think :p', '2023-03-02 05:44:43', 0, 0, 'How to change your oil without getting dirty!', 0, '[Discussion]'),
(2, 11, 1, '1. Don\'t be an idiot', '2023-03-02 22:03:51', 1, 0, 'READ BEFORE POSTING', 0, '[Admin]'),
(3, 12, 5, 'Gaza mi seh! Vybz Kartel fi president\n#FreeWorlBoss', '2023-03-23 07:04:32', 0, 1, 'Free Vybz Kartel', 0, '[Suggestion]'),
(4, 14, 6, 'hi hi', '2023-03-23 07:13:25', 0, -1, 'nice to see you here', 0, '[Discussion]'),
(5, 14, 6, '[DELETED]', '2023-03-23 07:17:52', 0, -2, '[DELETED]', 1, '[Discussion]'),
(6, 14, 6, '[DELETED]', '2023-03-23 07:18:02', 0, -2, '[DELETED]', 1, '[Discussion]'),
(7, 14, 6, '[DELETED]', '2023-03-23 07:18:08', 0, -2, '[DELETED]', 1, '[Discussion]'),
(8, 14, 6, '[DELETED]', '2023-03-23 07:18:13', 0, -2, '[DELETED]', 1, '[Discussion]'),
(9, 14, 6, '[DELETED]', '2023-03-23 07:18:18', 0, -2, '[DELETED]', 1, '[Discussion]'),
(10, 14, 6, '[DELETED]', '2023-03-23 07:18:25', 0, -2, '[DELETED]', 1, '[Discussion]'),
(11, 14, 6, '[DELETED]', '2023-03-23 07:18:51', 0, -2, '[DELETED]', 1, '[Discussion]'),
(12, 14, 6, '[DELETED]', '2023-03-23 07:19:02', 0, -2, '[DELETED]', 1, '[Discussion]'),
(13, 14, 1, '[DELETED]', '2023-03-23 07:26:51', 0, 0, '[DELETED]', 1, '[Admin]'),
(14, 3, 7, 'I just hate it.', '2023-03-23 07:39:36', 0, 1, 'Kelowna kinda suck!!!', 0, '[Discussion]'),
(15, 7, 9, 'meow    \n\n\n \n\n \n  \n\n\n\n \n                                 meow', '2023-03-23 18:19:59', 0, -2, 'meow', 0, '[Discussion]'),
(16, 14, 10, 'wd', '2023-03-23 19:08:39', 0, 1, 'da', 0, '[Discussion]'),
(17, 11, 1, 'hi', '2023-03-23 23:24:42', 0, 1, 'test', 0, '[Admin]'),
(18, 1, 12, 'i love tomatos', '2023-03-24 02:16:05', 0, -2, 'mmmmmm', 0, '[Suggestion]'),
(19, 1, 13, 'are vegetables even real, or do we only eat fruits?', '2023-03-24 04:12:26', 0, 0, 'What is a fruit?', 0, '[Discussion]'),
(20, 5, 13, 'Can\'t believe I am the first one to post in here. Big eyes... am I right?', '2023-03-24 04:13:42', 0, 0, 'I\'m the first!', 0, '[Discussion]'),
(21, 4, 13, 'There was weather, sports, and extra crap. ', '2023-03-24 04:14:49', 0, 0, 'Some News Today', 0, '[Discussion]');

-- --------------------------------------------------------

--
-- Table structure for table `rated`
--

CREATE TABLE `rated` (
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rated`
--

INSERT INTO `rated` (`post_id`, `user_id`, `rating`) VALUES
(3, 5, 1),
(4, 1, -1),
(4, 6, 1),
(4, 13, -1),
(5, 1, -1),
(5, 7, -1),
(6, 1, -1),
(6, 7, -1),
(7, 1, -1),
(7, 7, -1),
(8, 1, -1),
(8, 7, -1),
(9, 1, -1),
(9, 7, -1),
(10, 1, -1),
(10, 7, -1),
(11, 1, -1),
(11, 7, -1),
(12, 1, -1),
(12, 7, -1),
(14, 7, 1),
(15, 4, -1),
(15, 9, -1),
(16, 1, 1),
(17, 1, 1),
(18, 4, -1),
(18, 12, -1);

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE `replies` (
  `reply_id` int NOT NULL,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `content` text COLLATE utf8mb4_general_ci NOT NULL,
  `parent_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `replies`
--

INSERT INTO `replies` (`reply_id`, `post_id`, `user_id`, `content`, `parent_id`, `created_at`) VALUES
(1, 1, 3, '[USER HAS BEEN BANNED]', NULL, '2023-03-22 20:01:15'),
(2, 1, 2, 'Wait... you have a wife?', 1, '2023-03-22 20:01:15'),
(3, 1, 1, 'Or just... dont get covered in oil?', NULL, '2023-03-22 20:01:15'),
(4, 1, 2, 'Okay wow, my first post on your website and you reply like that? Thanks.', 3, '2023-03-22 20:01:15'),
(5, 1, 1, 'lmao sucks', 4, '2023-03-22 20:01:15'),
(6, 3, 5, 'Gonna have to agree with you on that one, Boss. ', NULL, '2023-03-23 07:09:33'),
(7, 4, 6, 'cool', NULL, '2023-03-23 07:13:30'),
(8, 4, 6, 'cool', 7, '2023-03-23 07:13:34'),
(9, 4, 6, '[DELETED]', 8, '2023-03-23 07:13:43'),
(10, 4, 6, 'cool', 9, '2023-03-23 07:13:47'),
(11, 14, 1, 'If you like gentrification and old people its great!', NULL, '2023-03-23 08:21:18'),
(12, 14, 4, 'Not a big fan.', NULL, '2023-03-23 08:24:42'),
(13, 15, 11, '<b>WOOF WOOF WOOF WOO ROO ROO ROO BARK ABRK<b>', NULL, '2023-03-23 19:13:00'),
(14, 15, 9, 'If you\'re looking for substitutions, high proof bourbon works best. I\'d recommend Maker\'s Mark or Buffalo Trace. That being said, I don\'t think the reply in all caps (or the hostile tone) was necessary. This is a civil discussion on how to make a classic Bourbon Old Fashioned. This is the alcohol discussion board after all. I wouldn\'t expect a standard poodle(?) like yourself to be able to properly enjoy a drink like this. This is a drink that has been enjoyed by the best since 1806. Start by using one of the two bourbons I recommended, the rule being that if you wouldn’t sip it by itself it has no place at the helm of a Bourbon Old Fashioned. (There are other whiskey drinks for masking subpar booze—this isn’t one of them.) From there, the cocktail-minded seem to break into two camps: simple syrup or muddled sugar.\nWhile a barspoon of syrup can cut your prep time in half, it robs the drink of some of the weight and texture that provides its deep appeal. If you want to make the drink like they did back in the 19th century, granulated sugar or a sugar cube is the way to go. If you want to make the cocktail with more of a modern twist, opt for simple syrup. (Although what’s the big rush? The Bourbon Old Fashioned isn’t going anywhere.) Just know that simple syrup adds a bit more water to your drink, so you may need to adjust your ice and stirring accordingly. \n\n', 13, '2023-03-24 00:06:11');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int NOT NULL,
  `post_id` int DEFAULT NULL,
  `reply_id` int DEFAULT NULL,
  `topic_id` int DEFAULT NULL,
  `report` text COLLATE utf8mb4_general_ci NOT NULL,
  `hero_id` int NOT NULL,
  `villain_id` int NOT NULL,
  `focus` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `resolved` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`report_id`, `post_id`, `reply_id`, `topic_id`, `report`, `hero_id`, `villain_id`, `focus`, `resolved`, `created_at`) VALUES
(2, NULL, 10, NULL, 'just for test', 6, 6, 'reply', 1, '2023-03-23 07:13:58'),
(3, NULL, 12, NULL, 'stupid\n', 8, 4, 'reply', 0, '2023-03-23 19:31:14');

-- --------------------------------------------------------

--
-- Table structure for table `stats`
--

CREATE TABLE `stats` (
  `deletedPosts` int DEFAULT NULL,
  `bannedUsers` int DEFAULT NULL,
  `uniqueToday` int DEFAULT NULL,
  `uniqueMonth` int DEFAULT NULL,
  `uniqueTotal` int DEFAULT NULL,
  `postsToday` int DEFAULT NULL,
  `logDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stats`
--

INSERT INTO `stats` (`deletedPosts`, `bannedUsers`, `uniqueToday`, `uniqueMonth`, `uniqueTotal`, `postsToday`, `logDate`) VALUES
(9, 1, 0, 0, 0, 0, '2023-12-20 19:43:33');

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `topic_id` int NOT NULL,
  `topic_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `topic_img` varchar(40) COLLATE utf8mb4_general_ci NOT NULL,
  `topic_bio` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topics`
--

INSERT INTO `topics` (`topic_id`, `topic_name`, `topic_img`, `topic_bio`) VALUES
(1, 'fruit', 'img/topics/fruit.png', 'Anything and Everything Fruit and fruity!'),
(2, 'usa', 'img/topics/usa.png', '\'MERICA'),
(3, 'canada', 'img/topics/canada.png', 'The true north strong and free!'),
(4, 'news', 'img/topics/news.png', 'If you post here you probably read the newspaper.'),
(5, 'anime', 'img/topics/anime.png', 'こにちは！ All things Anime!'),
(6, 'movies', 'img/topics/movie.png', 'Luke, This is the movies board'),
(7, 'alcohol', 'img/topics/alcohol.png', 'The only hobby that is also a problem!'),
(8, 'business', 'img/topics/business.png', 'This board is for the people who make more than they deserve!'),
(9, 'software', 'img/topics/software.png', 'Hello World!'),
(10, 'wall Street', 'img/topics/wallStreet.png', '\'Nobody Knows If A Stock\'s Going Up, Down Or F***ing Sideways, Least Of All Stockbrokers. But We Have To Pretend We Know.\' -Wolf of Wall Street'),
(11, 'cars', 'img/topics/cars.png', 'New cars, Used cars, Car mods, Trucks, Offroading, if it involves an engine and four wheels it belongs here! (no PT Cruisers)'),
(12, 'music', 'img/topics/music.png', 'Strings, Reeds, Keys or keyboards, we dont care! Post it here!'),
(13, 'tv', 'img/topics/tv.png', '\'I am not in danger Skylar, I AM the danger! A guy opens his door and gets shot, and you think that of me? No, I AM the one who knocks.\'- Breaking Bad'),
(14, 'general', 'img/topics/general.png', 'This is for things that don\'t belong anywhere else, just like you :)'),
(15, 'DIY', 'img/topics/diy.png', 'Do it yourself! Will it be cheaper? Maybe! Will it be better? Absolutely not!');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `PASSWORD` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `profile_pic` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'img/user/sword.png',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `user_bio` varchar(120) COLLATE utf8mb4_general_ci DEFAULT 'Please be nice, I''m new.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `PASSWORD`, `email`, `is_admin`, `profile_pic`, `banned`, `user_bio`) VALUES
(1, 'mason', 'e43854df32d574e0a680f71b86bf7353', 'admin@example.com', 1, 'img/user/admin.png', 0, 'Please be nice, I\'m new.'),
(2, 'Kirbyfan101', '1caa76eecff5ea4659403bacbfff2b53', 'kirby@fake.com', 0, 'img/user/sword.png', 0, 'Please be nice, I\'m new.'),
(3, 'RealAlien123', '5f4dcc3b5aa765d61d8327deb882cf99', 'alien@spaceship.com', 0, 'img/user/banned.png', 1, '[USER IS BANNED]'),
(4, 'ItsMeLiam', '855bc409ac50949ae076617f3e479032', 'hi6577@gmail.com', 1, 'img/user/4.png', 0, 'Yuh'),
(5, 'ravioli', 'e3660ec29affb80033c5b34eb82a01f8', 'ravioli@gmail.com', 0, 'img/user/sword.png', 0, 'Please be nice, I\'m new.'),
(6, 'hello', '646e613efcfc1317061b1df9340e3726', 'hello@gmail.com', 0, 'img/user/sword.png', 0, 'test'),
(7, 'Stranley', 'c222ba9dab54f4ebaf1db2d16a833537', 'stranleyf@gmail.com', 1, 'img\\user\\vanguard.png', 0, 'Please be nice, I\'m new.'),
(8, 'Jordo', 'd7061c4c0586646cdb0c65616bcd017a', 'jordanjkroberts@gmail.com', 1, 'img/user/8.png', 0, 'yeah'),
(9, 'sabo_the_cat', '234fab67bc96c0e2414dfd49c881cee8', 'killerak86@gmail.com', 0, 'img/user/9.png', 0, 'mow'),
(10, 'Kenneth', '2c103f2c4ed1e59c0b4e2e01821770fa', 'kenneth@kenneth.com', 0, 'img\\user\\sword.png', 0, 'faefaefaefdfc'),
(11, 'sterling_the_dog', 'c71e656053f1f08e346e9c69fe6e3480', 'sterling@gmail.com', 0, 'img/user/11.png', 0, 'Please be nice, I\'m new.'),
(12, 'imnearyou', '956517d264f96fd69ee180bec6f41b75', 'stephendoesnotcare@gmail.com', 0, 'img\\user\\mage.png', 0, 'window'),
(13, 'ihatethis', '3e964979c0073a81e80459e88af37ffd', '123@hotmail.ca', 0, 'img\\user\\mage.png', 0, 'Please be nice, I\'m new.'),
(14, 'jord', '8b25a882ad9eea4b0fc0394dfd5b5a45', 'jord@somewhere.com', 0, 'img\\user\\spear.png', 0, 'Please be nice, I\'m new.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `banned`
--
ALTER TABLE `banned`
  ADD PRIMARY KEY (`ban_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rated`
--
ALTER TABLE `rated`
  ADD PRIMARY KEY (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `reply_id` (`reply_id`),
  ADD KEY `hero_id` (`hero_id`),
  ADD KEY `villain_id` (`villain_id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`topic_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banned`
--
ALTER TABLE `banned`
  MODIFY `ban_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `replies`
--
ALTER TABLE `replies`
  MODIFY `reply_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `topic_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `banned`
--
ALTER TABLE `banned`
  ADD CONSTRAINT `banned_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `banned_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`topic_id`),
  ADD CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `rated`
--
ALTER TABLE `rated`
  ADD CONSTRAINT `rated_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`),
  ADD CONSTRAINT `rated_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `replies`
--
ALTER TABLE `replies`
  ADD CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`),
  ADD CONSTRAINT `replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `replies_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `replies` (`reply_id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`topic_id`) REFERENCES `posts` (`topic_id`),
  ADD CONSTRAINT `reports_ibfk_3` FOREIGN KEY (`reply_id`) REFERENCES `replies` (`reply_id`),
  ADD CONSTRAINT `reports_ibfk_4` FOREIGN KEY (`hero_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reports_ibfk_5` FOREIGN KEY (`villain_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


#DOG THING

CREATE DATABASE dog_surveys;
USE dog_surveys;
CREATE table IF NOT EXISTS responses
(
    id int NOT NULL AUTO_INCREMENT,
    response TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    username VARCHAR(50) NOT NULL,
    DOG_NAME VARCHAR(50) NOT NULL,
    image_link TEXT,
	PRIMARY KEY (id)
);