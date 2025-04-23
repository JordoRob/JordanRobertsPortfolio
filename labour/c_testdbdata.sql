
USE testdb;

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
('testuser3', md5('password'), false, false, NOW());


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