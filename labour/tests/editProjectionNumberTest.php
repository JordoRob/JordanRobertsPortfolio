<?php

use PHPUnit\Framework\TestCase;


include_once dirname(__DIR__) . '/src/session_security.php';
include_once dirname(__DIR__) . '/src/job-view/editProjectionNumber.php';


class editProjectionNumberTest extends TestCase
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


    public function testEditProjectionNumberUpdateExistingRecord()
    {
        // inputs
        $job_id = 1;
        $date = '2023-07-01';
        $count = 6;

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure a succesful result
        $this->assertEquals($code, "success");

        // grab result and see if its changed
        $sql = "SELECT * FROM outlook WHERE job_id = '$job_id' AND date = '$date'";
        foreach ($this->pdo->query($sql) as $row) {
            $this->assertEquals($count, $row['count']);
        }
    }
    public function testEditProjectionNumberNewRecord()
    {
        // generating random numbers since testdb doesn't get refreshed every time I run tests, so there's a very small chance this might update an existing record instead of inserting a new one, but it should be gauranteed on drone, however
        $job_id = 1;
        $date = rand(1000, 3000) . '-0' . rand(1, 9) . '-01';
        $count = rand(0, 500000);

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure a succesful result
        $this->assertEquals($code, "success");

        // grab result and see if its changed
        $sql = "SELECT * FROM outlook WHERE job_id = '$job_id' AND date = '$date'";
        foreach ($this->pdo->query($sql) as $row) {
            $this->assertEquals($count, $row['count']);
        }
    }

    public function testEditProjectionNumberCountTooBig()
    {
        // inputs
        $job_id = 1;
        $date = '2023-07-01';
        $count = 2147483648;

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure a succesful result
        $this->assertEquals($code, "toobig");

        // grab result and see if its changed
        $sql = "SELECT * FROM outlook WHERE job_id = '$job_id' AND date = '$date'";
        foreach ($this->pdo->query($sql) as $row) {
            $this->assertNotEquals($count, $row['count']);
        }
    }
    public function testEditProjectionNumberCountNotNumber()
    {
        // inputs
        $job_id = 1;
        $date = '2023-07-01';
        $count = '🤔';

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure a succesful result
        $this->assertEquals($code, "NaN");

        // grab result and see if its changed
        $sql = "SELECT * FROM outlook WHERE job_id = '$job_id' AND date = '$date'";
        foreach ($this->pdo->query($sql) as $row) {
            $this->assertNotEquals($count, $row['count']);
        }
    }
    public function testEditProjectionNumberBadDateFormatBadDay()
    {
        // inputs
        $job_id = 1;
        $date = '2023-07-02';
        $count = '3';

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure error thrown
        $this->assertEquals($code, "baddateformat");


        // inputs
        $job_id = 1;
        $date = '2023-07-00';
        $count = '3';

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure error thrown
        $this->assertEquals($code, "baddateformat");



        // inputs
        $job_id = 1;
        $date = '2023-07-32';
        $count = '3';

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure error thrown
        $this->assertEquals($code, "baddateformat");



        // inputs
        $job_id = 1;
        $date = '2023-07-';
        $count = '3';

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure error thrown
        $this->assertEquals($code, "baddateformat");


        // inputs
        $job_id = 1;
        $date = '2023-07-A0';
        $count = '3';

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure error thrown
        $this->assertEquals($code, "baddateformat");

    }
    public function testEditProjectionNumberBadDateFormatBadMonth()
    {
        // inputs
        $job_id = 1;
        $date = '2023-13-01';
        $count = '3';

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure error thrown
        $this->assertEquals($code, "baddateformat");



        // inputs
        $job_id = 1;
        $date = '2023-00-01';
        $count = '3';

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure error thrown
        $this->assertEquals($code, "baddateformat");



        // inputs
        $job_id = 1;
        $date = '2023-A0-01';
        $count = '3';

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure error thrown
        $this->assertEquals($code, "baddateformat");
    }
    public function testEditProjectionNumberBadDateFormatBadYear()
    {
        //inputs
        $job_id = 1;
        $date = '1-07-02';
        $count = '3';

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure error thrown
        $this->assertEquals($code, "baddateformat");

        //inputs
        $job_id = 1;
        $date = '-07-02';
        $count = '3';

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure error thrown
        $this->assertEquals($code, "baddateformat");

        //inputs
        $job_id = 1;
        $date = '99999-07-02';
        $count = '3';

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure error thrown
        $this->assertEquals($code, "baddateformat");

        //inputs
        $job_id = 1;
        $date = 'abab-07-02';
        $count = '3';

        // run function
        $code = editProjectionNumber($this->pdo, $job_id, $date, $count);

        //ensure error thrown
        $this->assertEquals($code, "baddateformat");

    }

}