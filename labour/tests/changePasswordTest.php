<?php

require_once dirname(__DIR__) . '/src/admin-view/admin-functions/change_password.php';
use PHPUnit\Framework\TestCase;

class ChangePasswordTest extends TestCase
{
    private $pdo;
    private $lastDummyId;

    protected function setUp(): void
    {
        $connString = "mysql:host=mysql-server;dbname=testdb";
        $user = "root";
        $password = "secret";
        $this->pdo = new PDO($connString, $user, $password);
        $this->createDummy();
    }

    protected function tearDown(): void
    {
        $this->deleteDummy();
        $this->pdo = null;
    }

    private function createDummy()
    {
        $this->pdo->query("INSERT INTO user (username, password, is_admin, created_at) VALUES ('testdummy', md5('TestPassword1$'), false, NOW())");
        $this->lastDummyId = $this->pdo->lastInsertId();
    }

    private function deleteDummy()
    {
        $this->pdo->query("DELETE FROM user WHERE id = $this->lastDummyId");
    }

    public function testSuccessfulPasswordChange()
    {
        $result = changePassword($this->pdo, 'TestPassword1$', 'NewPassword2!', 'NewPassword2!', $this->lastDummyId);
        $this->assertEquals('success', $result);
    }

    public function testSamePassword()
    {
        $result = changePassword($this->pdo, 'TestPassword1$', 'TestPassword1$', 'TestPassword1$', $this->lastDummyId);
        $this->assertEquals('same_password', $result);
    }

    public function testInvalidPassword()
    {
        $result = changePassword($this->pdo, 'TestPassword1$', 'weakpass', 'weakpass', $this->lastDummyId);
        $this->assertEquals('invalid_password', $result);
    }

    public function testPasswordMismatch()
    {
        $result = changePassword($this->pdo, 'TestPassword1$', 'NewPassword2!', 'ConfirmPassword3#', $this->lastDummyId);
        $this->assertEquals('password_mismatch', $result);
    }

    public function testIncorrectOldPassword()
    {
        $result = changePassword($this->pdo, 'WrongPassword', 'NewPassword2!', 'NewPassword2!', $this->lastDummyId);
        $this->assertEquals('incorrect_old_password', $result);
    }

}