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

    public function getAllUsersLike($keyword = null)
    {
        if ($keyword && is_string($keyword)) {
            
            $data = $this->db->get('users', array('username', 'LIKE', '%' . $keyword . '%'));

            if ($data->count()) {
                return $data->results();
            }
        }
        return false;
    }

    public function getUserFollows($user = null)
    {
        if ($user && is_numeric($user)) {
            $results = $this->db->get('follows', array('follower_id', '=', $user));

            if ($results->count()) {
                return $results->results();
            }
        }
        return false;
    }

    public function getUserFollowers($user = null)
    {
        if ($user && is_numeric($user)) {
            $results = $this->db->get('follows', array('followed_id', '=', $user));

            if ($results->count()) {
                return $results->results();
            }
        }
        return false;
    }

    public function addFollow($follower, $followed)
    {
        $fields = array(
            'follower_id' => $follower,
            'followed_id' => $followed,
            'created_at' => date('Y-m-d H:i:s'),
        );

        return $this->db->insert('follows', $fields);
    }

    public function deleteFollow($follower, $followed)
    {
        return $this->db->query('DELETE from follows where follower_id = ? AND followed_id = ? ', array($follower, $followed));
    }

    public function updateUser($user_id, $fields = [])
    {
        return $this->db->update('users', array(
            'field' => 'user_id',
            'value' => $user_id
        ), $fields);
    }

    public function deleteUser($user)
    {
    }
}
