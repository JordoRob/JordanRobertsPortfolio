<?php

use PHPUnit\Framework\TestCase;
include_once dirname(__DIR__) . '/src/Currently unused files/archiveEmployee.php';

class ArchiveEmployeeTest extends TestCase
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

    public function testArchiveEmployee()
    {
        $eid = 2;   //assigned to job with id=2;

        $result = archiveEmployee($eid, $this->pdo);

        //assert that the employee record is still in the database
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM employee WHERE id = $eid AND archived = CURDATE()");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        $this->assertTrue($result);
        $this->assertEquals(1, $count);

        //assert that the employee is removed from all assigned jobs
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM worksOn WHERE employee_id = $eid");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        $this->assertEquals(0, $count);

    }

}