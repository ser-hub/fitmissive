<?php

namespace Application\Services;

use Application\Repositories\ExerciseRepository;
use Application\Utilities\Validator;

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

    public function addExercise($category, $title, $description)
    {
        if (is_string($category)) {
            $category = $this->exerciseRepository->getCategory($category)->exercise_category_id;
        }

        if ($this->validateData($title, $description) && $category != false) {
            return $this->exerciseRepository->add([
                'category' => $category,
                'name' => $title,
                'description' => $description
            ]);
        } else {
            return 'Липсващи или твърде дълги данни';
        }
    }

    public function updateExercise($id, $title, $description)
    {
        if ($this->validateData($title, $description)) {
            return $this->exerciseRepository->update([
                'id' => $id,
                'name' => $title,
                'description' => $description
            ]);
        } else {
            return 'Липсващи или твърде дълги данни';
        }
    }

    public function deleteExercise($id)
    {
        return $this->exerciseRepository->delete($id);
    }

    public function getAllExerciseNames()
    {
        $raw_data = $this->exerciseRepository->getComplete();
        $result = [];

        if ($raw_data) {
            foreach ($raw_data as $row) {
                $result[$row->category_name][] = $row->exercise_name;
            }
        }

        return $result;
    }

    public function getAllExerciseData()
    {
        $raw_data = $this->exerciseRepository->getComplete();
        $result = [];

        if ($raw_data) {
            foreach ($raw_data as $row) {
                $result[$row->category_name][] = [
                    'id' => $row->exercise_id,
                    'name' => $row->exercise_name,
                    'description' => $row->exercise_description
                ];
            }
        }

        return $result;
    }

    public function exerciseExists($exercise_name)
    {
        return $this->exerciseRepository->find($exercise_name);
    }

    public function getCategoryOf($exercise_name)
    {
        $allData = $this->getAllExerciseNames();
        foreach ($allData as $category => $exercises) {
            if (in_array($exercise_name, $exercises)) {
                return $category;
            }
        }
    }

    private function validateData($title, $description)
    {
        $validator = new Validator();
        $validator->check([
            'title' => $title,
            'description' => $description
        ], [
            'title' => [
                'name' => 'Заглавието',
                'required' => true,
                'min' => 2,
                'max' => 100,
            ],
            'description' => [
                'name' => 'Описанието',
                'min' => 2,
                'max' => 1000,
            ]
        ]);

        return $validator->passed();
    }
}
