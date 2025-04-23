<?php

use PHPUnit\Framework\TestCase;


include_once dirname(__DIR__) . '/src/session_security.php';
include_once dirname(__DIR__) . '/src/job-details/archive_job.php';


class archiveJobTest extends TestCase
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


    public function testArchiveJobSuccess()
    {
        // inputs
        $job_id = 1;

        // make job_id's archived is set to null (since database is not reset)
        $stmt = $this->pdo->prepare("UPDATE job SET archived = NULL WHERE id = ?");
        $stmt->execute([$job_id]);

        // run function
        $code = archive_job($this->pdo, $job_id);

        //ensure a succesful result
        $this->assertEquals($code, "success");

        // grab result and see if the job was archived
        $stmt = $this->pdo->prepare("SELECT archived FROM job WHERE id = ?");
        $stmt->execute([$job_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotNull($result['archived']);

    }
    
    public function testArchiveJobAlreadyArchived()
    {
        // inputs
        $job_id = 1;

        // run function twice
        $code = archive_job($this->pdo, $job_id);
        $code = archive_job($this->pdo, $job_id);

        //ensure a succesful result
        $this->assertEquals($code, "jobalreadyarchived");

        // grab result and see if the job is still archived
        $stmt = $this->pdo->prepare("SELECT archived FROM job WHERE id = ?");
        $stmt->execute([$job_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotNull($result['archived']);

    }

    public function testArchiveJobJobDoesntExist()
    {
        // inputs
        $job_id = 5000000;

        // run function
        $code = archive_job($this->pdo, $job_id);

        //ensure a succesful result
        $this->assertEquals($code, "jobdoesntexist");

    }

    public function testArchiveJobUnassignEmployeesFromWorkson()
    {
        // inputs
        $job_id = 1;
        $emp_id = 3;

        // assign employees to job (workson)
        $stmt = $this->pdo->prepare("INSERT INTO worksOn (employee_id, job_id) VALUES (?, ?)");
        $stmt->execute([$emp_id, $job_id]);


        // run function
        $code = archive_job($this->pdo, $job_id);

        //ensure a succesful result
        $this->assertEquals($code, "success");

        // grab result and see if the job was archived
        $stmt = $this->pdo->prepare("SELECT archived FROM job WHERE id = ?");
        $stmt->execute([$job_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotNull($result['archived']);

        // make sure employee was unassigned off of workson
        $stmt = $this->pdo->prepare("SELECT * FROM worksOn WHERE employee_id = ? AND job_id = ?");
        $stmt->execute([$emp_id, $job_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($result);

    }

    public function testArchiveJobEndEmployeeAssignments()
    {
        // inputs
        $job_id = 1;
        $emp_id = 3;

        // assign employees to job (workson)
        $stmt = $this->pdo->prepare("INSERT INTO worksOn (employee_id, job_id) VALUES (?, ?)");
        $stmt->execute([$emp_id, $job_id]);

        // add an assignment to for the employee with startdate of now
        $stmt = $this->pdo->prepare("INSERT INTO assignments (employee_id, job_id, start_date) VALUES (?, ?, NOW())");
        $stmt->execute([$emp_id, $job_id]);

        // run function
        $code = archive_job($this->pdo, $job_id);

        //ensure a succesful result
        $this->assertEquals($code, "success");

        // grab result and see if the job was archived
        $stmt = $this->pdo->prepare("SELECT archived FROM job WHERE id = ?");
        $stmt->execute([$job_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotNull($result['archived']);

        // make sure employee was unassigned off of workson
        $stmt = $this->pdo->prepare("SELECT * FROM worksOn WHERE employee_id = ? AND job_id = ?");
        $stmt->execute([$emp_id, $job_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($result);

        // make sure employee end date was set to now
        $stmt = $this->pdo->prepare("SELECT end_date FROM assignments WHERE employee_id = ? AND job_id = ?");
        $stmt->execute([$emp_id, $job_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotNull($result['end_date']);

    }
    
    
    
}