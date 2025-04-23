<?php

include dirname(__DIR__) . '/src/employee info overlay/update_employee.php';
use PHPUnit\Framework\TestCase;

class editEmployeeTest extends TestCase
{
    protected $pdo;

    protected function setUp(): void
    {
        $connString = "mysql:host=mysql-server;dbname=testdb";
        $user = "root";
        $password = "secret";
        $this->pdo = new PDO($connString, $user, $password);
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
    }

    public function testUpdateEmp(): void
    {
        $emp_id = 2; 
        $stmt = $this->pdo->prepare("SELECT * FROM employee WHERE id = ?");
        $stmt->execute([$emp_id]);
        $originalEmployee = $stmt->fetch(PDO::FETCH_ASSOC);

        //modify employee details
        $name = "Updated Name";
        $phone = "(123) 456-7890";
        $phoneSec = "(987) 654-3210";
        $email = "updated@example.com";
        $datehired = "2023-01-01";
        $datearchived = "2023-08-10";
        $birthday = "1990-05-15";
        $notes = "Updated notes";
        $title = 2;
        $active = 1; 

        updateEmployee($this->pdo, $emp_id, $name, $phone, $phoneSec, $email, $datehired, $datearchived, $birthday, $notes, $title, $active);

        $stmt = $this->pdo->prepare("SELECT * FROM employee WHERE id = ?");
        $stmt->execute([$emp_id]);
        $updatedEmployee = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals($name, $updatedEmployee['name']);
        $this->assertEquals($phone, $updatedEmployee['phoneNum']);
        $this->assertEquals($phoneSec, $updatedEmployee['phoneNumSecondary']);
        $this->assertEquals($email, $updatedEmployee['email']);
        $this->assertEquals($datehired, $updatedEmployee['hired']);
        $this->assertEquals($datearchived, $updatedEmployee['archived']);
        $this->assertEquals($birthday, $updatedEmployee['birthday']);
        $this->assertEquals($notes, $updatedEmployee['notes']);
        $this->assertEquals($title, $updatedEmployee['role']); 
        $this->assertEquals($active, $updatedEmployee['active']);
    }
}