<?php

namespace Application\Repositories;

use Application\Database\DB;

class SplitRepository
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

    public function getAllSplits()
    {
        return $this->db->query("SELECT * FROM splits");
    }

    public function getSplitId($user_id, $day)
    {
        $day = $this->parseDay($day);
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
        $splits = array(
            'Monday' => $this->db->get('mondays', array('user_id', '=', $user_id))->first(),
            'Tuesday' => $this->db->get('tuesdays', array('user_id', '=', $user_id))->first(),
            'Wednesday' => $this->db->get('wednesdays', array('user_id', '=', $user_id))->first(),
            'Thursday' => $this->db->get('thursdays', array('user_id', '=', $user_id))->first(),
            'Friday' => $this->db->get('fridays', array('user_id', '=', $user_id))->first(),
            'Saturday' => $this->db->get('saturdays', array('user_id', '=', $user_id))->first(),
            'Sunday' => $this->db->get('sundays', array('user_id', '=', $user_id))->first()
        );

        return $splits;
    }

    public function insertSplit($user_id, $day, $data = [])
    {
        return $this->db->insert($this->parseDay($day), array(
            'user_id' => $user_id,
            'title' => $data['title'],
            'description' => $data['description']
        ));
    }

    public function updateSplit($day, $id, $data = [])
    {
        $day = $this->parseDay($day);

        return $this->db->update($day, array(
            'field' => substr($day, 0, strlen($day) - 1) . '_id',
            'value' => $id
        ), array(
            'title' => $data['title'],
            'description' => $data['description']
        ));
    }

    private function parseDay($day)
    {
        switch ($day) {
            case 'Monday':
                $day = 'mondays';
                break;
            case 'Tuesday':
                $day = 'tuesdays';
                break;
            case 'Wednesday':
                $day = 'wednesdays';
                break;
            case 'Thursday':
                $day = 'thursdays';
                break;
            case 'Friday':
                $day = 'fridays';
                break;
            case 'Saturday':
                $day = 'saturdays';
                break;
            case 'Sunday':
                $day = 'sundays';
                break;
        }

        return $day;
    }
}
