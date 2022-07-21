<?php

namespace Application\Repositories;

use Application\Database\DB;

class SplitRepository
{
    private static $instance = null;
    private $db;
    private $dayTables = [];

    private function __construct()
    {
        $this->db = DB::getInstance();
        $this->dayTables = [
            'Monday' => 'mondays',
            'Tuesday' => 'tuesdays',
            'Wednesday' => 'wednesdays',
            'Thursday' => 'thursdays',
            'Friday' => 'fridays',
            'Saturday' => 'saturdays',
            'Sunday' => 'sundays'
        ];
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getAllSplits()
    {
        return $this->db->query("SELECT * FROM splits");
    }

    public function getSplitId($user_id, $day)
    {
        $day = $this->dayTables[$day];
        $day = substr($day, 0, strlen($day) - 1);
        $property = $day . '_id';
        return $this->db->action(
            'SELECT ' . $day . '_id ',
            $day . 's',
            array('user_id', '=', intval($user_id))
        )->first()->$property;
    }

    public function getUserSplits($user_id)
    {
        $splits = [];

        foreach ($this->dayTables as $day => $table) {
            $splits[$day] = $this->db->get($table, array('user_id', '=', $user_id))->first();
        }

        return $splits;
    }

    public function insertSplit($user_id, $day, $data = [])
    {
        return $this->db->insert($this->dayTables[$day], array(
            'user_id' => $user_id,
            'title' => $data['title'],
            'description' => $data['description']
        ));
    }

    public function updateSplit($day, $id, $data = [])
    {
        $day = $this->dayTables[$day];

        return $this->db->update($day, array(
            'field' => substr($day, 0, strlen($day) - 1) . '_id',
            'value' => $id
        ), array(
            'title' => $data['title'],
            'description' => $data['description']
        ));
    }

    public function deleteUserSplits($userId)
    {
        $result = null;

        foreach ($this->dayTables as $table) {
            $result = $this->db->delete($table, array('user_id', '=', $userId));
        }

        return $result;
    }
}
