<?php
include_once dirname(__DIR__) . '/src/admin-view/admin-functions/create_manager.php';
use PHPUnit\Framework\TestCase;

class CreateManagerTest extends TestCase
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

    public function testCreateManager()
    {
        //case for success user creation
        $username = 'testuser';
    
        $response = createManager($this->pdo, $username);
    
        if (is_string($response)) {
            $this->assertEquals('USERNAME_TAKEN', $response);
        } 
        elseif (is_array($response) && isset($response['code'])) {
            if ($response['code'] === 'USER_CREATED') {
                $responseData = $response['data'];
    
                $this->assertArrayHasKey('temp_pass', $responseData);
                $this->assertEquals($username, $responseData['username']);
    
                //check if user created in the db
                $stmt = $this->pdo->prepare("SELECT * FROM user WHERE id = :id");
                $stmt->execute([':id' => $responseData['id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
                $this->assertNotNull($user);
                $this->assertEquals($username, $user['username']);
    
                //temp password validation
                $expectedTempPass = $username . substr($responseData['temp_pass'], strlen($username));
                $this->assertEquals(md5($expectedTempPass), $user['password']);
            } elseif ($response['code'] === 'USERNAME_TAKEN') {
                $this->fail('Username is taken');
            } else {
                $this->fail('Invalid response from createManager');
            }
        } else {
            $this->fail('Invalid response from createManager');
        }
    }

    public function testCreateManagerOutOfRange(){
        //username length less than 5 characters
        $usernameShort = 'abc'; 
        $res = createManager($this->pdo, $usernameShort);
        $this->assertEquals('USERNAME_RANGE', $res);
    
        //username length more than 15 characters
        $usernameLong = 'verylongusernamethatisoutofrange'; 
        $res = createManager($this->pdo, $usernameLong);
        $this->assertEquals('USERNAME_RANGE', $res);
    }

    public function testCreateManagerNameTaken(){
        //username already taken
        $usernameTaken = 'testuser'; // Assuming 'testuser' already inserted from previous codes
        $res = createManager($this->pdo, $usernameTaken);
        $this->assertEquals('USERNAME_TAKEN', $res);
    }

}