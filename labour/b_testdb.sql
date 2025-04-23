
CREATE DATABASE IF NOT EXISTS testdb;
USE testdb;

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
