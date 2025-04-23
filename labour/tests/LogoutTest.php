
<?php

include_once dirname(__DIR__). '/src/processLogout.php'; // Include the file containing the login function
use PHPUnit\Framework\TestCase;


class LogoutTest extends TestCase
{
    /**
     * @test
     * @runInSeparateProcess
     */
    public function testLogout()
    {
        $this->assertTrue(logout());
    }
}

