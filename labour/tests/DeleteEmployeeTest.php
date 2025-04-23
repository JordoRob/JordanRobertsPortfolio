<?php

use PHPUnit\Framework\TestCase;
include_once dirname(__DIR__) . '/src/employee info overlay/deleteEmployee.php';

class DeleteEmployeeTest extends TestCase
{
    private $pdo;

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

    public function testDeleteEmployeeAssigned()
    {
        $eid = 1;   //assigned to job with id=1;

        $result = deleteEmployee($eid, $this->pdo);
        $this->assertTrue($result);


        //assert that the employee record is still in the database
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM employee WHERE id = $eid");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        $this->assertEquals(0, $count);
    }

    public function testDeleteEmployeeNotAssigned()
    {
        
        $eid = 10;// not assigned to a jon

        $result = deleteEmployee($eid, $this->pdo);
        $this->assertTrue($result);

        //assert that the employee record is removed from the database
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM employee WHERE id = $eid");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        $this->assertEquals(0, $count);
    }

    public function testDeleteEmployeeWrongCredential()
    {
        $eid = 99999;// no such employee in the table

        $result = deleteEmployee($eid, $this->pdo);
        $this->assertTrue($result);

        //assert that the employee record is removed from the database
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM employee WHERE id = $eid");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        $this->assertEquals(0, $count);
    }
}