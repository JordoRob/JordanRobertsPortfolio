<?php
include_once dirname(__DIR__) . '/src/job-view/addJob.php';
use PHPUnit\Framework\TestCase;

class AddJobTest extends TestCase
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

    public function testAddJob(): void
    {
        $title = "Software Developer";
        $address = "123 Main St";
        $archived = null;
        $manager = "John Doe";
        $start_date = "2023-08-01";
        $end_date = "2023-08-31";

        $jobId = addJob($title, $address, $archived, $manager, $start_date, $end_date, $this->pdo);

        $this->assertIsString($jobId, "Job ID should be an integer");

        // Fetch the inserted job from the database and verify its attributes
        $stmt = $this->pdo->prepare("SELECT * FROM job WHERE id = :jobId");
        $stmt->bindValue(':jobId', $jobId, PDO::PARAM_INT);
        $stmt->execute();
        $job = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals($title, $job['title']);
        $this->assertEquals($address, $job['address']);
        $this->assertNull($job['archived']);
        $this->assertEquals($manager, $job['manager_name']);
        $this->assertEquals($start_date, $job['start_date']);
        $this->assertEquals($end_date, $job['end_date']);
    }

}