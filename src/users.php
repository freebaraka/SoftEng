<?php
namespace App;

class User
{
    private string $username;
    private string $password;
    private string $role;

    public function __construct(string $username, string $password, string $role = 'customer')
    {
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function checkPassword(string $password): bool
    {
        return $this->password === $password;
    }
}
