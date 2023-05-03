<?php 

namespace Application\Repositories;

use Application\Database\DB;

class ExerciseRepository {
    private static $instance = null;
    private $db;
    private const EXERCISE_CATEGORY_TABLE = "exercise_category";
    private const EXERCISE_TABLE = "exercise";

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

    public function getAllExerciseCategories()
    {
        $data = $this->db->query('SELECT * FROM ' . self::EXERCISE_CATEGORY_TABLE);

        if ($data->count()) {
            return $data->results();
        }
    }

    public function getAllExercises()
    {
        $data = $this->db->query('SELECT * FROM ' . self::EXERCISE_TABLE);

        if ($data->count()) {
            return $data->results();
        }
    }

    public function getComplete()
    {
        $data = $this->db->query('SELECT * FROM ' . self::EXERCISE_CATEGORY_TABLE . ' ec join exercise e on ec.exercise_category_id = e.category_id');

        if ($data->count()) {
            return $data->results();
        }
    }

    public function find($exercise_name)
    {
        if ($exercise_name) {
            $data = $this->db->get(self::EXERCISE_TABLE, ['exercise_name', '=', $exercise_name]);

            if ($data->count()) {
                return $data->first();
            }
        }
        return false;
    }

    public function getCategory($category_name) {
        if ($category_name) {
            $data = $this->db->get(self::EXERCISE_CATEGORY_TABLE, ['category_name', '=', $category_name]);

            if ($data->count()) {
                return $data->first();
            }
        }
        return false;
    }

    public function add($exercise)
    {
        $fields = [
            'category_id' => $this->getCategory($exercise['category']),
            'exercise_name' => $exercise['name'],
            'exercise_description' => $exercise['description']
        ];

        return $this->db->insert(self::EXERCISE_TABLE, $fields);
    }

    public function delete($exercise_name) {
        $this->db->delete(self::EXERCISE_TABLE, ['info_id', '=', $this->find($exercise_name)->exercise_id]);
    }
}