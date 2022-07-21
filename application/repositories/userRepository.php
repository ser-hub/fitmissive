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

    public function updateUser($user_id, $fields = [])
    {
        return $this->db->update('users', array(
            'field' => 'user_id',
            'value' => $user_id
        ), $fields);
    }

    public function deleteUser($userId)
    {
        return $this->db->delete('users', array('user_id', '=', $userId));
    }

    public function find($user = null)
    {
        if ($user) {
            $field = (is_numeric($user)) ? 'user_id' : 'username';
            $data = $this->db->query('SELECT * FROM users ' .
                'INNER JOIN roles ON users.role_id = roles.role_id WHERE ' . $field . ' = ?', array($user));

            if ($data->count()) {
                return $data->first();
            }
        }
        return false;
    }

    public function getAllUsersLike($keyword = null, $exclude = null, $from = 0, $count = null)
    {
        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM users WHERE';
        $params = [];
        if ($keyword && is_string($keyword)) {
            $sql = $sql . ' username LIKE ?';
            $params[] = $keyword . '%';
        }
        if ($exclude != null && is_numeric($exclude)) {
            $sql = $sql . ' AND user_id != ?';
            $params[] = $exclude;
        }

        $sql = $sql . ' ORDER BY username ASC LIMIT';

        if ($from != null && $from > 0) {
            $sql = $sql . ' ?,';
            $params[] = $from;
        } 
        if (is_numeric($count) && $count > 0) {
            $sql = $sql . ' ?';
            $params[] = $count;
        }

        $data = $this->db->query($sql, $params);

        if ($data->count()) {
            return [
                'users' => $data->results(),
                'total' => $this->db->query('SELECT FOUND_ROWS() as total')->first()->total
            ];
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
}
