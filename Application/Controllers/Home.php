<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Services\SplitService;
use Application\Utilities\{Redirect, Time};

class Home extends Controller
{
    private $splitService;

    public function __construct()
    {
        $this->splitService = SplitService::getInstance();

        parent::__construct();
    }

    public function index()
    {
        $this->view('home/home', [
            'color' => $this->userService->getUserColor($this->loggedUser),
            'workout' => $this->splitService->splitsOf($this->loggedUser),
            'followedWorkouts' => $this->splitService->getFollowedSplits(
                $this->loggedUser,
                $this->userService->getFollowsArrayOf($this->loggedUser)
            )
        ]);
    }

    public function logout()
    {
        $this->userService->logout();
        Redirect::to('/index');
    }
}
