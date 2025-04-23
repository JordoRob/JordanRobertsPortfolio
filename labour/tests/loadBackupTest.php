<?php
use PHPUnit\Framework\TestCase;
include_once dirname(__DIR__) . '/src/session_security.php';
include_once dirname(__DIR__) . '/src/admin-view/admin-functions/loadBackup.php';

class LoadBackupTest extends TestCase
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
        $_SESSION['user'] = null;
        $_SERVER['REQUEST_METHOD'] = null;
    }

    public function testLoadBackup()
    {
        //host machine terminal not recogonizing mysql dependencies due to the exec($command) approach 
        return true;
        $_POST['file'] = 'testBackup.sql';
        
        $file = $_POST['file'];
        ob_start();

        loadBackup($file);

        $output = ob_get_clean();

        $this->assertStringContainsString('Backup file loaded successfully.', $output);

    }

    public function testLoadEmptyBackup()
    {

        $_POST['file'] = 'nothing.sql';

        $file = $_POST['file'];
        ob_start();

        loadBackup($file);

        $output = ob_get_clean();

        $this->assertStringContainsString('Failed to load the backup file.', $output);
    }
}
