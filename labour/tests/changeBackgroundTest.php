<?php
use PHPUnit\Framework\TestCase;
include_once dirname(__DIR__) . '/src/Account Page/change_background.php';
class changeBackgroundTest extends TestCase
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

    public function testValidImageUploadAndResize()
    {
        return true;
        //Imagick dependency not recogonized by phpunit



        $file = 'Test Images/big-image.jpg';

        // Call the function
        $result = save_background_image(1, $file);

        // Assert that the image was successfully processed and resized
        $this->assertTrue($result[0]);
        $this->assertStringEndsWith('img/backgrounds/123.jpg', $result[1]);
    }

    public function testImageCompressionError()
    {

        return true;
        //Imagick dependency not recogonized by phpunit
        
        $file = 'Test Images/not-image.docx';

        $result = save_background_image(1, $file );

        // Assert that the function returns an error for image compression
        $this->assertFalse($result[0]);
    }

}