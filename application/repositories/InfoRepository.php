<?php

namespace Application\Repositories;

use Application\Database\DB;

class InfoRepository
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

    public function getAll()
    {
        $data = $this->db->query('SELECT * FROM info');

        if ($data->count()) {
            return $data->results();
        }
    }

    public function add($info)
    {
        $fields = array(
            'title' => $info['title'],
            'slug' => $info['slug'],
            'content' => $info['content'],
            'created_at' => date('Y-m-d H:i:s'),
        );

        return $this->db->insert('info', $fields);
    }

    public function find($info = null)
    {
        if ($info) {
            $field = (is_numeric($info)) ? 'info_id' : 'slug';
            $data = $this->db->get('info', array($field, '=', $info));

            if ($data->count()) {
                return $data->first();
            }
        }
        return false;
    }

    public function update($title, $fields)
    {
        return $this->db->update('info', array(
            'field' => 'title',
            'value' => $title
        ), $fields);
    }

    public function delete($id)
    {
        $this->db->delete('info', array('info_id', '=', $id));
    }
}