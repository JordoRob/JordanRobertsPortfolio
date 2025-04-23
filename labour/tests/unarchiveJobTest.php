<?php

use PHPUnit\Framework\TestCase;


include_once dirname(__DIR__) . '/src/session_security.php';
include_once dirname(__DIR__) . '/src/job-details/unarchive_job.php';


class unarchiveJobTest extends TestCase
{

    protected function setUp(): void
    {
        $connString = "mysql:host=mysql-server;dbname=testdb";
        $user = "root";
        $password = "secret";
        $this->pdo = new PDO($connString, $user, $password);
        
        // start transaction
        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        // rollback transaction
        $this->pdo->rollBack();
        
        // close connection
        $this->pdo = null;

    }


    public function testUnarchiveJobSuccess()
    {
        // inputs
        $job_id = 1;

        // make job_id's archived is set to now (since database is not reset)
        $stmt = $this->pdo->prepare("UPDATE job SET archived = NOW() WHERE id = ?");
        $stmt->execute([$job_id]);

        // run function
        $code = unarchive_job($this->pdo, $job_id);

        //ensure a succesful result
        $this->assertEquals($code, "success");

        // grab result and see if the job was archived
        $stmt = $this->pdo->prepare("SELECT archived FROM job WHERE id = ?");
        $stmt->execute([$job_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNull($result['archived']);

    }
    
    public function testArchiveJobAlreadyUnarchived()
    {
        // inputs
        $job_id = 1;

        // run function twice
        $code = unarchive_job($this->pdo, $job_id);
        $code = unarchive_job($this->pdo, $job_id);

        //ensure a succesful result
        $this->assertEquals($code, "jobalreadyunarchived");

        // grab result and see if the job is still archived
        $stmt = $this->pdo->prepare("SELECT archived FROM job WHERE id = ?");
        $stmt->execute([$job_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNull($result['archived']);

    }

    public function testArchiveJobJobDoesntExist()
    {
        // inputs
        $job_id = 5000000;

        // run function
        $code = unarchive_job($this->pdo, $job_id);

        //ensure a succesful result
        $this->assertEquals($code, "jobdoesntexist");

    }
    
    
}