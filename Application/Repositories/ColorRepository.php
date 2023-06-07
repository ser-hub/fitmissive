<?php

namespace Application\Repositories;

use Application\Database\DB;

class ColorRepository
{
    private static $instance = null;
    private $db;
    private const COLORS_TABLE = "colors";

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
        $data = $this->db->query('SELECT * FROM ' . self::COLORS_TABLE);

        if ($data->count()) {
            return $data->results();
        }
    }

    public function getColorByHex($value)
    {
        $data = $this->db->get(self::COLORS_TABLE, ['color_hex', '=', $value]);

        if ($data->count()) {
            return $data->first();
        }
        return false;
    }

    public function update($id, $value) 
    {
        return $this->db->update(self::COLORS_TABLE, [
            'field' => 'color_id',
            'value' => $id
        ], [ 'color_hex' => $value ]);
    }
}
