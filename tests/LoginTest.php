<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private $validUser = 'teacher@example.com';
    private $validPassword = 'secret';

    protected function setUp(): void
    {
        $GLOBALS['test_valid_user'] = $this->validUser;
        $GLOBALS['test_valid_hash'] = hash('sha256', $this->validPassword, false);
    }

    private function runCheck(string $user, string $pass): string
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['username'] = $user;
        $_POST['password'] = $pass;
        ob_start();

        $script = file_get_contents(__DIR__ . '/../admin/files/checklogin.php');
        $script = str_replace('include "../../database/config.php";', 'include __DIR__ . "/TestConfig.php";', $script);
        eval('namespace TestEnv; ' . $script);

        return trim(ob_get_clean());
    }

    public function testValidCredentials(): void
    {
        $result = $this->runCheck($this->validUser, $this->validPassword);
        $this->assertSame('success', $result);
    }

    public function testInvalidCredentials(): void
    {
        $result = $this->runCheck('wrong@example.com', 'bad');
        $this->assertSame('fail', $result);
    }
}
