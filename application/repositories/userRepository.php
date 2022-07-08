<?php

namespace Application\Repositories;

use Application\Database\DB;

class UserRepository
{
    private static $instance = null;
    private $db;

    private function __construct()
    {
        $this->db = DB::getInstance();
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getAllUsers()
    {
        return $this->db->query('SELECT * FROM users');
    }

    public function addUser($user)
    {
        $fields = array(
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'salt' => $user->getSalt(),
            'created_at' => date('Y-m-d H:i:s'),
        );

        return $this->db->insert('users', $fields);
    }

    public function find($user = null)
    {
        if ($user) {
            $field = (is_numeric($user)) ? 'user_id' : 'username';
            $data = $this->db->get('users', array($field, '=', $user));

            if ($data->count()) {
                return $data->first();
            }
        }
        return false;
    }

    public function updateUser($user_id, $fields = [])
    {
        return $this->db->update('users', $user_id, $fields);
    }

    public function deleteUser($user)
    {
    }
}
