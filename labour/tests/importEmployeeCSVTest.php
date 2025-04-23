<?php

use PHPUnit\Framework\TestCase;
include_once dirname(__DIR__) . '/src/admin-view/admin-functions/insertEmployeeCSV.php';

class ImportEmployeeCSVTest extends TestCase
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
    public function testinsertEmployeeCSV()
    {
        $data1 = [
            [
                'role' => 0,
                'name' => 'test guy csv', 
                'active' => 0,
                'birthday' => '2029-07-01',
                'phoneNum1' => '7777777777', 
                'phoneNum2' => '7777777778', 
                'email' => 'email@example.com', 
                'hireDate' => '2029-06-01',
                'redseal' => 0 
            ],
        ];
    
        // Insert employee records into the database
        insertEmployees($data1, $this->pdo);
    
        // Check if the employee was inserted correctly
        $stmt = $this->pdo->prepare("SELECT * FROM employee WHERE name = :name");
        $stmt->bindValue(':name', 'test guy csv');
        $stmt->execute();
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Assert employee imported with correct values
        $this->assertEquals(0, $employee['role']);
        $this->assertEquals('test guy csv', $employee['name']);
        $this->assertEquals(0, $employee['active']);
        $this->assertNull($employee['archived']); // Employee considered active, so the archived value should be null
        $this->assertEquals('2029-07-01', $employee['birthday']);
        $this->assertEquals('7777777777', $employee['phoneNum']);
        $this->assertEquals('7777777778', $employee['phoneNumSecondary']);
        $this->assertEquals('email@example.com', $employee['email']);
        $this->assertEquals('2029-06-01', $employee['hired']);
    }
}