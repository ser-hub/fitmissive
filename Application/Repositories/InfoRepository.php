<?php

namespace Application\Repositories;

use Application\Database\DB;

class InfoRepository
{
    private static $instance = null;
    private $db;
    private const INFO_TABLE = "info";

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
        $data = $this->db->query('SELECT * FROM ' . self::INFO_TABLE);

        if ($data->count()) {
            return $data->results();
        }
    }

    public function add($info)
    {
        $fields = [
            'title' => $info['title'],
            'slug' => $info['slug'],
            'content' => $info['content'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert(self::INFO_TABLE, $fields);
    }

    public function find($info = null)
    {
        if ($info) {
            $field = (is_numeric($info)) ? 'info_id' : 'slug';
            $data = $this->db->get(self::INFO_TABLE, [$field, '=', $info]);

            if ($data->count()) {
                return $data->first();
            }
        }
        return false;
    }

    public function update($title, $fields)
    {
        return $this->db->update(self::INFO_TABLE, [
            'field' => 'title',
            'value' => $title
        ], $fields);
    }

    public function delete($id)
    {
        $this->db->delete(self::INFO_TABLE, ['info_id', '=', $id]);
    }
}