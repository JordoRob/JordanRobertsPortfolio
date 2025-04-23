<?php
include_once dirname(__DIR__). '/src/processLogin.php'; // Include the file containing the login function

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    // Valid login credentials
    private $username;
    private $password;
    
    // Invalid login credentials
    private $wrongUsername;
    private $wrongPassword;




    protected function setUp(): void
    {
        $connString = "mysql:host=mysql-server;dbname=testdb";
        $user = "root";
        $password = "secret";
        $this->pdo = new PDO($connString, $user, $password);

        $this->username = 'johnsmith';
        $this->password = 'password123';
        $this->wrongUsername = 'asdasdasdasd';
        $this->wrongPassword = 'qweqweqweqwe';
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
    }

    public function testValidLogin()
    {
        // Test valid login credentials
        $this->assertGreaterThan(-1, loginUser($this->username, $this->password, $this->pdo));
    }

    public function testInvalidLogin()
    {
        // Test invalid login credentials
        $this->assertEquals(-1, loginUser($this->wrongUsername , $this->wrongPassword, $this->pdo));
    }

    public function testEmptyUsername()
    {
        // Test empty username
        $this->assertEquals(-1, loginUser('', $this->password, $this->pdo));
    }

    public function testEmptyPassword()
    {
        // Test empty password
        $this->assertEquals(-1, loginUser($this->username, '', $this->pdo));
    }
}