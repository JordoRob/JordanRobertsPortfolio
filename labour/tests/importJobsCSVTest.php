<?php

use PHPUnit\Framework\TestCase;
include_once dirname(__DIR__) . '/src/admin-view/admin-functions/insertJobsCSV.php';

class ImportJobsCSVTest extends TestCase
{

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


    //test case for inserting a job record where all fields are non emptyn enddate > today which means job is still active
    public function testImportJobNormal()
    {
        $data1 = [
            [
                'title' => 'test csv 1',
                'address' => '123 Main St',
                'foreman' => 'John Smith', //employee id = 1;
                'manager' => 'Manager 1',
                'startDate' => '2023-07-01',
                'endDate' => '2029-07-01'
            ],
        ];
    
        insertJobs($data1, $this->pdo);

        $stmt = $this->pdo->prepare("SELECT * FROM job WHERE title = :title");
        $stmt->bindValue(':title', 'test csv 1');
        $stmt->execute();
        $job = $stmt->fetch(PDO::FETCH_ASSOC);
        $job_id = $job['id'];



        // Assert job imported with correct values

        $this->assertEquals('test csv 1', $job['title']);
        $this->assertNull($job['archived']); // Job considered active, so the archived value should be null
        $this->assertEquals('123 Main St', $job['address']);
        $this->assertEquals('2023-07-01', $job['start_date']);
        $this->assertEquals('2029-07-01', $job['end_date']);

    }
    

    //job considered active
    public function testImportJobEmptyEndDate()
    {
        $data3 = [
            [
                'title' => 'test csv 3',
                'address' => '123 Main St',
                'foreman' => 'John Smith', //employee id = 1;
                'manager' => 'Manager 1',
                'startDate' => '2023-07-01',
            ],
        ];

        insertJobs($data3, $this->pdo);

        $stmt = $this->pdo->prepare("SELECT * FROM job WHERE title = :title");
        $stmt->bindValue(':title', 'test csv 3');
        $stmt->execute();
        $job = $stmt->fetch(PDO::FETCH_ASSOC);
        $job_id = $job['id'];

        // Assert job imported with correct values

        $this->assertEquals('test csv 3', $job['title']);
        $this->assertNull($job['archived']); // Job considered active, so the archived value should be null
        $this->assertEquals('123 Main St', $job['address']);
        $this->assertEquals('2023-07-01', $job['start_date']);
        $this->assertEquals(NULL, $job['end_date']);

    }

    // Insertion should fail due to the null job title
    public function testImportJobNullTitle()
    {
        $data4 = [
            [
                'title' => NULL,
                'address' => '123 Main St',
                'foreman' => 'Foreman 1', //employee id = 1;
                'manager' => 'Manager 1',
                'startDate' => '2023-07-03',
            ],
        ];

        insertJobs($data4, $this->pdo);

        $stmt = $this->pdo->prepare("SELECT * FROM job WHERE title = :title");
        $stmt->bindValue(':title', NULL);
        $stmt->execute();
        $job = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($job); // Assert job deson't exist

    }
}