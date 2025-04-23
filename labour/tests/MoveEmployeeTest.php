<?php

use PHPUnit\Framework\TestCase;

class MoveEmployeeTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        $connString = "mysql:host=mysql-server;dbname=testdb";
        $user = "root";
        $password = "secret";
        $this->pdo = new PDO($connString, $user, $password);
        $_SESSION['user'] =  $this->lastdummyUSERid;
        date_default_timezone_set("America/Vancouver");
        include_once dirname(__DIR__) . '/src/job-view/move_employee.php';

    }

    protected function tearDown(): void
    {
        $this->pdo = null;
    }

    private function createDummy($active) {  //creates a user and an employee      
        $this->pdo->query("INSERT INTO user (username, password, is_admin, created_at) VALUES ('testdummy', md5('TestPassword1$'), true, NOW())");
        $this->lastdummyUSERid = $this->pdo->lastInsertId();
        $this->pdo->query("INSERT INTO employee (name, role, active) VALUES ('testdummy', 1, $active)");
        $this->lastdummyEMPid = $this->pdo->lastInsertId();

    }
    private function deleteDummy() {    //deletes all of these things we have created
        $this->pdo->query("DELETE FROM user WHERE id = $this->lastdummyUSERid");
        $this->pdo->query("DELETE FROM assignments WHERE employee_id = $this->lastdummyEMPid");
        $this->pdo->query("DELETE FROM worksOn WHERE employee_id = $this->lastdummyEMPid");
        $this->pdo->query("DELETE FROM employee WHERE id = $this->lastdummyEMPid");
    }

    public function testAddAssignment_Valid_Inactive(){ //Employee is inactive, should make them active and then add them to workson
        $this->createDummy(1);

        $emp=$this->lastdummyEMPid;

        $endjob=1;

        $code = add_assignment($this->pdo,$emp,$endjob);

        $stmt1 = $this->pdo->prepare("SELECT start_date,employee_id,job_id FROM assignments WHERE employee_id=? AND job_id=? AND end_date IS NULL");
        $stmt1->execute([$emp,$endjob]);
        $assign=$stmt1->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals("success",$code);   //Code success
        $this->assertEquals(date("Y-m-d"),$assign['start_date']);   //added to assignments with the proper date

        $stmt2 = $this->pdo->prepare("SELECT * FROM worksOn WHERE employee_id=? AND job_id=?");
        $stmt2->execute([$emp,$endjob]);
        $this->assertEquals(1,$stmt2->rowCount());  //This entry does in fact exist

        $stmt3 = $this->pdo->prepare("SELECT active FROM employee WHERE id=?");
        $stmt3->execute([$emp]);
        $empRow=$stmt3->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(0,$empRow['active']);   //made the inactive employee active


        $this->deleteDummy();
    }
    public function testAddAssignment_Invalid(){    //Employee already works on this job
        $this->createDummy(1);

        $emp=$this->lastdummyEMPid;

        $endjob=1;
        $stmt = $this->pdo->prepare("INSERT INTO worksOn (employee_id, job_id) VALUES (?, ?)");
        $stmt->execute([$emp,$endjob]);
        $code = add_assignment($this->pdo,$emp,$endjob);

        $this->assertEquals("tooreal",$code);   //code failed

        $stmt1 = $this->pdo->prepare("SELECT start_date,employee_id,job_id FROM assignments WHERE employee_id=? AND job_id=? AND end_date IS NULL");
        $stmt1->execute([$emp,$endjob]);
        $this->assertEquals(0,$stmt1->rowCount());  //no assignment was created

        $stmt2 = $this->pdo->prepare("SELECT * FROM worksOn WHERE employee_id=? AND job_id=?");
        $stmt2->execute([$emp,$endjob]);
        $this->assertEquals(1,$stmt2->rowCount());  //no duplicate rows in workson  

        $this->deleteDummy();
    }

    public function testDeleteAssignment_Valid_DeletesAssignment(){ //Since the start and end date are the same, this should remove that row from the db
        $this->createDummy(0);

        $emp=$this->lastdummyEMPid;

        $endjob=1;
        $now = date("Y-m-d");
        add_assignment($this->pdo,$emp,$endjob);    //add the assignment

        $code = delete_assignment($this->pdo,$emp,$endjob);     

        $this -> assertEquals("success",$code); //code success

        $stmt1 = $this->pdo->prepare("SELECT * FROM worksOn WHERE employee_id=? AND job_id=?");
        $stmt1->execute([$emp,$endjob]);
        $this->assertEquals(0,$stmt1->rowCount());  //remove the workson row

        $stmt2 = $this->pdo->prepare("SELECT * FROM assignments WHERE employee_id=? AND job_id=? AND end_date=?");
        $stmt2->execute([$emp,$endjob,$now]);
        $this->assertEquals(0,$stmt2->rowCount());  //Deleted the assignments tab since they werent on it for more than a day


        $this->deleteDummy();
    }
    public function testDeleteAssignment_Valid_KeepAssignment(){    //This one should keep the assignment since they are seperate days
        $this->createDummy(0);

        $emp=$this->lastdummyEMPid;

        $endjob=1;
        $past = new DateTime();
        date_sub($past,date_interval_create_from_date_string("2 days"));
        $past=date_format($past,"Y-m-d");
        add_assignment($this->pdo,$emp,$endjob,$past);  //give it a start date in the past


        $code= delete_assignment($this->pdo,$emp,$endjob);

        $this -> assertEquals("success",$code); //code success

        $stmt1 = $this->pdo->prepare("SELECT * FROM worksOn WHERE employee_id=? AND job_id=?");
        $stmt1->execute([$emp,$endjob]);
        $this->assertEquals(0,$stmt1->rowCount());  //deleted workson row

        $stmt2 = $this->pdo->prepare("SELECT * FROM assignments WHERE employee_id=? AND job_id=? AND start_date=?");
        $stmt2->execute([$emp,$endjob,$past]);
        $assign=$stmt2->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(date("Y-m-d"),$assign['end_date']);   //this row should still be here since they were removed 2 days later

        $this->deleteDummy();
    }
    public function testDeleteAssignment_Invalid(){ //just dont make an assignment to delete
        $this->createDummy(0);

        $emp=$this->lastdummyEMPid;

        $endjob=1;

        $code= delete_assignment($this->pdo,$emp,$endjob);

        $this->assertEquals("notreal",$code);   //code should fail


        $this->deleteDummy();
    }
    public function testMoveAssignment_Valid_DeletesAssignment(){   //much the same as delete but with a third step
        $this->createDummy(0);

        $emp=$this->lastdummyEMPid;

        $startjob=1;
        $endjob=2;
        $now = date("Y-m-d");
        add_assignment($this->pdo,$emp,$startjob);  //create a starting assignment

        $code = move_assignment($this->pdo,$emp,$startjob,$endjob); 

        $stmt1 = $this->pdo->prepare("SELECT * FROM assignments WHERE employee_id=? AND job_id=? AND start_date=?");
        $stmt1->execute([$emp,$startjob,$now]);
        $this->assertEquals(0,$stmt1->rowCount());  //old assignment shouldnt exist since start and end date are the same

        $stmt2 = $this->pdo->prepare("SELECT * FROM assignments WHERE employee_id=? AND job_id=? AND end_date IS NULL");
        $stmt2->execute([$emp,$endjob]);
        $assign=$stmt2->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals($now,$assign['start_date']);    //new assignment should exist and start date is today

        $stmt3 = $this->pdo->prepare("SELECT * FROM worksOn WHERE employee_id=? AND job_id=?");
        $stmt3->execute([$emp,$endjob]);
        $this->assertEquals(1,$stmt3->rowCount());  //new workson entry should be there

        $stmt4 = $this->pdo->prepare("SELECT * FROM worksOn WHERE employee_id=? AND job_id=?");
        $stmt4->execute([$emp,$startjob]);
        $this->assertEquals(0,$stmt4->rowCount());  //old one should not

        $this->assertEquals("success",$code);
    }

    public function testMoveAssignment_Valid_KeepsAssignment(){ //we going in the past again
        $this->createDummy(0);

        $emp=$this->lastdummyEMPid;

        $startjob=1;
        $endjob=2;
        $past = new DateTime();
        date_sub($past,date_interval_create_from_date_string("2 days"));
        $past=date_format($past,"Y-m-d");
        $now = date("Y-m-d");
        add_assignment($this->pdo,$emp,$startjob,$past);    //make an assignment that started 2 days ago

        $code = move_assignment($this->pdo,$emp,$startjob,$endjob);

        $stmt1 = $this->pdo->prepare("SELECT * FROM assignments WHERE employee_id=? AND job_id=? AND start_date=?");
        $stmt1->execute([$emp,$startjob,$past]);
        $assign1=$stmt1->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals($now,$assign1['end_date']); //old assignment should exist with an end date of today

        $stmt2 = $this->pdo->prepare("SELECT * FROM assignments WHERE employee_id=? AND job_id=? AND start_date=?");
        $stmt2->execute([$emp,$endjob,$now]);
        $assign2=$stmt2->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals($now,$assign2['start_date']);   //new assignment should exist with a start date of today!

        $stmt3 = $this->pdo->prepare("SELECT * FROM worksOn WHERE employee_id=? AND job_id=?");
        $stmt3->execute([$emp,$endjob]);
        $this->assertEquals(1,$stmt3->rowCount());  //new workson should exist

        $stmt4 = $this->pdo->prepare("SELECT * FROM worksOn WHERE employee_id=? AND job_id=?");
        $stmt4->execute([$emp,$startjob]);
        $this->assertEquals(0,$stmt4->rowCount());  //old workson should not

        $this->assertEquals("success",$code);
    }

    public function testMoveAssignment_Invalid(){   //again just dont make an assignment to move them from
        $this->createDummy(0);

        $emp=$this->lastdummyEMPid;

        $startjob=1;
        $endjob=2;

        $code = move_assignment($this->pdo,$emp,$startjob,$endjob);

        $stmt1 = $this->pdo->prepare("SELECT * FROM assignments WHERE employee_id=? AND job_id=? AND start_date=?");
        $stmt1->execute([$emp,$endjob,$past]);
        $this->assertEquals(0,$stmt1->rowCount());  //nothing should happen on all of these

        $stmt2 = $this->pdo->prepare("SELECT * FROM worksOn WHERE employee_id=? AND job_id=?");
        $stmt2->execute([$emp,$startjob]);
        $this->assertEquals(0,$stmt2->rowCount());

        $stmt3 = $this->pdo->prepare("SELECT * FROM worksOn WHERE employee_id=? AND job_id=?");
        $stmt3->execute([$emp,$endjob]);
        $this->assertEquals(0,$stmt3->rowCount());

        $this->assertEquals("notreal",$code);
    }
}