<?php

namespace Application\Repositories;

use Application\Database\DB;

class SplitRepository
{
    private static $instance = null;
    private $db;
    private const DAYS_TABLES = [
        'Monday' => 'mondays',
        'Tuesday' => 'tuesdays',
        'Wednesday' => 'wednesdays',
        'Thursday' => 'thursdays',
        'Friday' => 'fridays',
        'Saturday' => 'saturdays',
        'Sunday' => 'sundays'
    ];
    private const RATINGS_TABLE = "ratings";

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

    public function getSplitId($user_id, $day)
    {
        $day = self::DAYS_TABLES[$day];
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

        foreach (self::DAYS_TABLES as $day => $table) {
            $splits[$day] = $this->db->get($table, array('user_id', '=', $user_id))->first();
        }

        return $splits;
    }

    public function insertSplit($user_id, $day, $data = [])
    {
        return $this->db->insert(self::DAYS_TABLES[$day], [
            'user_id' => $user_id,
            'title' => $data['title'],
            'description' => $data['description']
        ]);
    }

    public function updateSplit($day, $id, $data = [])
    {
        $day = self::DAYS_TABLES[$day];

        return $this->db->update($day, [
            'field' => substr($day, 0, strlen($day) - 1) . '_id',
            'value' => $id
        ], $data);
    }

    public function deleteUserSplits($userId)
    {
        $result = null;

        foreach (self::DAYS_TABLES as $table) {
            $result = $this->db->delete($table, array('user_id', '=', $userId));
        }

        return $result;
    }

    public function getAllRatingsOf($user_id) {
        return $this->db->action('SELECT *', self::RATINGS_TABLE, [
            'target_id', '=', $user_id
        ])->results();
    }

    public function insertRating($user_id, $rated_id, $rating) {
        return $this->db->insert(self::RATINGS_TABLE, [
            'user_id' => $user_id,
            'target_id' => $rated_id,
            'rating' => $rating
        ]);
    }

    public function updateRating($rating_id, $rating) {
        $this->db->update(self::RATINGS_TABLE, [
            'field' => 'rating_id',
            'value' => $rating_id
        ], ['rating' => $rating]);
    }
}
