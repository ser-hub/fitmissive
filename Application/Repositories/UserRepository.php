<?php

namespace Application\Repositories;

use Application\Database\DB;

class UserRepository
{
    private static $instance = null;
    private $db;
    private const USERS_TABLE = "users";
    private const FOLLOWS_TABLE = "follows";

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
        return $this->db->query('SELECT * FROM ' . self::USERS_TABLE);
    }

    public function addUser($user)
    {
        $fields = [
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'salt' => $user->getSalt(),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return $this->db->insert(self::USERS_TABLE, $fields);
    }

    public function updateUser($user_id, $fields = [])
    {
        return $this->db->update(self::USERS_TABLE, [
            'field' => 'user_id',
            'value' => $user_id
        ], $fields);
    }

    public function deleteUser($userId)
    {
        return $this->db->delete(self::USERS_TABLE, ['user_id', '=', $userId]);
    }

    public function find($user = null)
    {
        if ($user) {
            $field = (is_numeric($user)) ? 'user_id' : 'username';
            $data = $this->db->query('SELECT * FROM ' . self::USERS_TABLE .
                ' JOIN roles ON users.role_id = roles.role_id' .
                ' JOIN colors ON users.color_id = colors.color_id WHERE ' . $field . ' = ?', [$user]);

            if ($data->count()) {
                return $data->first();
            }
        }
        return false;
    }

    public function getUserByX($field, $value) {
        $data = $this->db->query('SELECT * FROM ' . self::USERS_TABLE . ' WHERE ' . $field . ' = ?', [$value]);

        if ($data->count()) {
            return $data->first();
        }
        return false;
    }

    public function getAllUsersLike($keyword = null, $exclude = null, $from = 0, $count = null)
    {
        $sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM ' . self::USERS_TABLE . ' WHERE';
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
                self::USERS_TABLE => $data->results(),
                'total' => $this->db->query('SELECT FOUND_ROWS() as total')->first()->total
            ];
        }
        return false;
    }

    public function getUserFollows($user = null)
    {
        if ($user && is_numeric($user)) {
            $results = $this->db->get(self::FOLLOWS_TABLE, ['follower_id', '=', $user]);

            if ($results->count()) {
                return $results->results();
            }
        }
        return false;
    }

    public function getUserFollowers($user = null)
    {
        if ($user && is_numeric($user)) {
            $results = $this->db->get(self::FOLLOWS_TABLE, ['followed_id', '=', $user]);

            if ($results->count()) {
                return $results->results();
            }
        }
        return false;
    }

    public function addFollow($follower, $followed)
    {
        $fields = [
            'follower_id' => $follower,
            'followed_id' => $followed,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert(self::FOLLOWS_TABLE, $fields);
    }

    public function deleteFollow($follower, $followed)
    {
        return $this->db->query('DELETE from ' . self::FOLLOWS_TABLE . ' where follower_id = ? AND followed_id = ? ', [$follower, $followed]);
    }

    public function deleteAllFollows($userId)
    {
        return $this->db->query('DELETE from ' . self::FOLLOWS_TABLE . ' where follower_id = ? OR followed_id = ? ', [$userId, $userId]);
    }
}
