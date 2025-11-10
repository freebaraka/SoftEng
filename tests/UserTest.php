<?php

use PHPUnit\Framework\TestCase;
use App\User;

require_once __DIR__ . '/../src/users.php';

class UserTest extends TestCase
{
    public function testUserCreation()
    {
        $user = new User('baraka','5678','admin');
        $this->assertEquals('baraka', $user->getUsername());
        $this->assertEquals('admin', $user->getRole());
    }

    public function testPasswordCheck()
    {
        $user = new User('baraka','5678','admin');
        $this->assertTrue($user->checkPassword('5678'));
        $this->assertFalse($user->checkPassword('wrongpass'));
    }
}
