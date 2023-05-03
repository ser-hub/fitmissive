<?php

namespace Application\Services;

use Application\Repositories\ExerciseRepository;

class ExerciseService
{
    private static $instance;
    private $exerciseRepository;
    
    private function __construct()
    {
        $this->exerciseRepository = ExerciseRepository::getInstance();
    }
    
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    public function getAllExerciseData() {
        $raw_data = $this->exerciseRepository->getComplete();
        $result = [];

        if ($raw_data)
        {
            foreach ($raw_data as $row) {
                $result[$row->category_name][] = $row->exercise_name;
            }
        }

        return $result;
    }

    public function exerciseExists($exercise_name) {
        return $this->exerciseRepository->find($exercise_name);
    }

    public function getCategoryOf($exercise_name) {
        $allData = $this->getAllExerciseData();
        foreach ($allData as $category => $exercises) {
            if (in_array($exercise_name, $exercises)) {
                return $category;
            }
        }
    }
}