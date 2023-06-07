<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Utilities\Constants;
use Application\Utilities\Redirect;
use Application\Utilities\Input;
use Application\Services\ColorService;
use Application\Services\ExerciseService;

class Admin extends Controller
{
    private $colorService,
            $exerciseService;
    private $status;

    public function __construct()
    {
        parent::__construct();
        if (!$this->userService->getLoggedUser()->role_name === Constants::USER_ROLE_ADMIN) {
            Redirect::to('/home');
        }

        $this->colorService = ColorService::getInstance();
        $this->exerciseService = ExerciseService::getInstance();
    }

    public function index()
    {
        $this->exercises();
    }

    public function exercises()
    {
        $this->view('admin/admin', [
            'section' => 'exercises',
            'error' => $this->status
        ]);
    }

    public function colors()
    {
        $this->view('admin/admin', [
            'section' => 'colors',
            'error' => $this->status
        ]); 
    }

    public function newExercise() {
        if (Input::exists()) {
            $result = $this->exerciseService->addExercise(
                Input::get('exercise-category'), 
                Input::get('exercise-title'),
                Input::get('exercise-description')
            );

            if(is_string($result)) {
                $this->status = $result;
            }
        }

        $this->exercises();
    }

    public function updateExercise($id) {
        if (Input::exists()) {
            $result = $this->exerciseService->updateExercise(
                $id, 
                Input::get('exercise-title'),
                Input::get('exercise-description')
            );

            if(is_string($result)) {
                $this->status = $result;
            }
        }
        $this->exercises();
    }

    public function deleteExercise($id) {
        if (!$this->exerciseService->deleteExercise($id)) {
            $this->status = 'Грешка при изтриването';
        }

        $this->exercises();
    }

    public function updateColor($id) {
        if (Input::exists()) {
            $result = $this->colorService->updateColor($id, Input::get('result'));
            if(is_string($result)) {
                $this->status = $result;
            }
        }
        $this->colors();
    }
}