<?php

use PHPUnit\Framework\TestCase;


include_once dirname(__DIR__) . '/src/session_security.php';
include_once dirname(__DIR__) . '/src/job-details/delete_job.php';


class deleteJobTest extends TestCase
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


    public function testDeleteJobSuccess()
    {
        // inputs
        $job_id = 4; // special delete job

        // run function
        $code = delete_job($this->pdo, $job_id);

        //ensure a succesful result
        $this->assertEquals($code, "success");

        // grab result and see if the job was deleted
        $stmt = $this->pdo->prepare("SELECT * FROM job WHERE id = ?");
        $stmt->execute([$job_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($result);
    }
    
    public function testDeleteJobErrorEmpsAssigned()
    {
        // inputs
        $job_id = 1;
        $emp_id = 3;
        
        // assign employee to job (worksOn)
        $stmt = $this->pdo->prepare("INSERT INTO worksOn (employee_id, job_id) VALUES (?, ?)");
        $stmt->execute([$emp_id, $job_id]);

        // run function
        $code = delete_job($this->pdo, $job_id);

        //ensure a succesful result
        $this->assertEquals($code, "empassigned");

        // grab result and see if the job was deleted
        $stmt = $this->pdo->prepare("SELECT * FROM job WHERE id = ?");
        $stmt->execute([$job_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals($result['id'], $job_id);
    }

    public function testDeleteJobJobDoesntExist()
    {
        // inputs
        $job_id = 5000000;

        // run function
        $code = delete_job($this->pdo, $job_id);

        //ensure a succesful result
        $this->assertEquals($code, "jobdoesntexist");

    }

    public function testDeleteJobDeleteOldAssignments()
    {
        // inputs
        $job_id = 4; // special delete job
        $emp_id = 3;
        $start_date = "2000-01-01";
        $end_date = "2000-01-02";

        // create an assignment with start date and end date of some time in past
        $stmt = $this->pdo->prepare("INSERT INTO assignments (employee_id, job_id, start_date, end_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$emp_id, $job_id, $start_date, $end_date]);

        // run function
        $code = delete_job($this->pdo, $job_id);

        //ensure a succesful result
        $this->assertEquals($code, "success");

        // grab result and see if the old assignment was deleted
        $stmt = $this->pdo->prepare("SELECT * FROM assignments WHERE employee_id = ? AND job_id = ? AND start_date = ? AND end_date = ?");
        $stmt->execute([$emp_id, $job_id, $start_date, $end_date]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($result);

    }
    
    
}