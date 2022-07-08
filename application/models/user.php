<?php

namespace Application\Models;

use Application\Utilities\Hash;

class User
{
    private $username;
    private $email;
    private $password;
    private $salt;

    public function __construct($username, $email, $password)
    {
        $this->salt = Hash::salt(16);
        $this->username = $username;
        $this->email = $email;
        $this->password = Hash::make($password, $this->salt);
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getSalt()
    {
        return $this->salt;
    }
}
