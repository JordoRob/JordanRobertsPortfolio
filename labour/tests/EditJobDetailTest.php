<?php

use PHPUnit\Framework\TestCase;
include_once dirname(__DIR__) . '/src/session_security.php';
include_once dirname(__DIR__) . '/src/job-details/update_job.php';

class EditJobDetailTest extends TestCase
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

    public function testEditJobDetail(){

        //('Job 1', NULL, 1, '2023-01-01', '2024-04-30')
        $id = 1;
        $title = 'changed title';
        $archived = NULL;
        $address = "somehwere";
        $manager_name = "someguy";
        $startDate = '2023-06-01';
        $endDate = '2023-06-11';

        $res = editJobDetail($id, $title, $manager_name, $address, $startDate, $endDate,"", $this->pdo);
        $stmt = $this->pdo->prepare("SELECT * FROM job WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    
        //check if job edit successful
        $job = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals($title, $job['title']);
        $this->assertEquals($archived, $job['archived']);
        $this->assertEquals($address, $job['address']);
        $this->assertEquals($manager_name, $job['manager_name']);
        $this->assertEquals($startDate, $job['start_date']);
        $this->assertEquals($endDate, $job['end_date']);
    }

}