<?php

include_once dirname(__DIR__) . '/src/admin-view/admin-functions/reset_password.php';
use PHPUnit\Framework\TestCase;

class resetPasswordTest extends TestCase
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

    public function testResetPasswordUserNotFound()
    {
        $response = resetPassword($this->pdo, -1);

        // Verify the response
        $this->assertEquals('Selected manager does not exist', $response);
    }

    public function testResetPasswordSuccess()
    {
        //insert a test user
        $stmt = $this->pdo->prepare("INSERT INTO user (username, password, is_admin) VALUES (?, ?, 0)");
        $stmt->execute(['testuser', md5('password')]);
        $userId = $this->pdo->lastInsertId();
    
        $response = resetPassword($this->pdo, $userId);
    
        if (is_string($response)) {
            $this->fail('Reset password failed with message: ' . $response);
        } elseif (is_array($response) && isset($response['code'])) {
            $this->assertEquals('Password reset successfully.', $response['code']);
            $this->assertArrayHasKey('data', $response);
            $this->assertArrayHasKey('temp_pass', $response['data']);
    
            // make sure the user's password was actually updated in the database
            $stmt = $this->pdo->prepare("SELECT * FROM user WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            $this->assertEquals(
                md5($user['username'] . substr($response['data']['temp_pass'], strlen($user['username']))),
                $user['password']
            );
        } else {
            $this->fail('Invalid response from resetPassword');
        }
    }
}