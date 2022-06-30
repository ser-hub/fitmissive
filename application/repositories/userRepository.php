<?php

class UserRepository
{
    private static $_instance = null;
    private $_db;

    private function __construct()
    {
        $this->_db = DB::getInstance();
    }

    public static function getInstance()
    {
        if (!isset(self::$_instance))
        {
            self::$_instance = new UserRepository();
        }

        return self::$_instance;
    }

    public function getAllUsers()
    {
        return $this->_db->query('SELECT * FROM users');
    }

    public function addUser($user)
    {
        if (!$this->_db->query('INSERT INTO users (name, email, password) VALUES (?, ?, ?)', array(
            $user->name,
            $user->email,
            $user->password
        ))->error())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function updateUser($user)
    {

    }

    public function deleteUser($user)
    {

    }
}