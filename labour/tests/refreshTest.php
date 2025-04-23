<?php

use PHPUnit\Framework\TestCase;

class refreshTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        $connString = "mysql:host=mysql-server;dbname=testdb";
        $user = "root";
        $password = "secret";
        $this->pdo = new PDO($connString, $user, $password);
        date_default_timezone_set("America/Vancouver");
        include_once dirname(__DIR__) . '/src/refresh/check-update.php';

    }

    protected function tearDown(): void
    {
        $this->pdo = null;
    }

    public function testJobRefresh(){
        sleep(1);   //since multiple tests run in literally milliseconds it always says there are new columns lol
        $time=time();
        $code = update_check(time(),"job",$this->pdo);
        $this->assertFalse($code[0]);
        $this->assertEquals("job has no recent updates.",$code[1]);
        $this->pdo->query("INSERT INTO job (title, manager_name) VALUES ('testjob', 'name')");
        $this->lastdummyid = $this->pdo->lastInsertId();
        $code2 = update_check($time,"job",$this->pdo);
        $this->assertTrue($code2[0]);
        $this->assertEquals("job has recent updates!",$code2[1]);

        $this->pdo->query("DELETE FROM job WHERE id = $this->lastdummyid");
    }
    public function testEmployeeRefresh(){
        sleep(1);   //since multiple tests run in literally milliseconds it always says there are new columns lol
        $time=time();
        $code = update_check(time(),"employee",$this->pdo);
        $this->assertFalse($code[0]);
        $this->assertEquals("employee has no recent updates.",$code[1]);
        $this->pdo->query("INSERT INTO employee (name, role, active) VALUES ('testdummy', 1, 0)");
        $this->lastdummyEMPid = $this->pdo->lastInsertId();
        $code2 = update_check($time,"employee",$this->pdo);
        $this->assertTrue($code2[0]);
        $this->assertEquals("employee has recent updates!",$code2[1]);
        
        $this->pdo->query("DELETE FROM employee WHERE id = $this->lastdummyEMPid");
    }
}