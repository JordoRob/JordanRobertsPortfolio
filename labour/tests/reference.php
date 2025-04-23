<?php
class ChangePasswordTest extends TestCase
{
    // Valid login credentials
    private $username;
    private $password;
    
    // Invalid login credentials
    private $wrongUsername;
    private $wrongPassword;

    public function __construct()
    {
        parent::__construct();

        $this->username = 'johnsmith';
        $this->password = 'password123';
        $this->wrongUsername = 'asdasdasdasd';
        $this->wrongPassword = 'qweqweqweqwe';
    }

    public function testValidLogin()
    {
        // Test valid login credentials
        $this->assertGreaterThan(-1, loginUser($this->username, $this->password));
    }

    public function testInvalidLogin()
    {
        // Test invalid login credentials
        $this->assertEquals(-1, loginUser($this->wrongUsername , $this->wrongPassword));
    }

    public function testEmptyUsername()
    {
        // Test empty username
        $this->assertEquals(-1, loginUser('', $this->password));
    }

    public function testEmptyPassword()
    {
        // Test empty password
        $this->assertEquals(-1, loginUser($this->username, ''));
    }
}

?>