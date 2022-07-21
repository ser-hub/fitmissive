<?php

namespace Application\Core;

use Application\Utilities\Redirect;
use Application\Services\{UserService, InfoService};

class Controller
{
    protected $loggedUser;
    protected $userService;
    private $infoService = null;

    public function __construct()
    {
        $this->userService = UserService::getInstance();
        $this->infoService = InfoService::getInstance();

        if (!$this->userService->isUserLoggedIn()) {
            Redirect::to('/index');
        }

        $this->loggedUser = $this->userService->getLoggedUser()->user_id;
    }

    protected function model($model)
    {
        require_once 'Application/Models/' . $model . '.php';
        return new $model;
    }

    protected function view($view, $data = [])
    {
        if ($this->loggedUser) {
            $data['loggedUser'] = $this->loggedUser;
        }

        if ($this->infoService == null) {
            $this->infoService = InfoService::getInstance();
        }
        $data['info'] = [];
        $data['info'] = $this->infoService->getAllInfo();
        require_once 'Application/Views/' . $view . '.php';
    }
}
