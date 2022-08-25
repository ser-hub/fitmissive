<?php

namespace Application\Core;

use Application\Utilities\Redirect;
use Application\Services\{UserService, InfoService, ChatService};

class Controller
{
    protected $loggedUser;
    protected $userService;
    protected $chatService;
    private $infoService = null;
    private $loggedUsername;

    public function __construct()
    {
        $this->userService = UserService::getInstance();
        $this->infoService = InfoService::getInstance();
        $this->chatService = ChatService::getInstance();

        if (!$this->userService->isUserLoggedIn()) {
            Redirect::to('/index');
        }

        $this->loggedUser = $this->userService->getLoggedUser();
        $this->loggedUsername = $this->loggedUser->username;
        $this->loggedUser = $this->loggedUser->user_id;
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
            $data['loggedUsername'] = $this->loggedUsername;
            $data['menu'] = [
                'Messenger' => '/messenger', 
                'My profile' => '/profile/' . $this->loggedUsername,
                'Sign out' => '/home/logout'
            ];
            $data['unseenMessages'] = $this->chatService->unseenMessages($this->loggedUser);
        }

        if ($this->infoService == null) {
            $this->infoService = InfoService::getInstance();
        }
        
        $data['info'] = [];
        $data['info'] = $this->infoService->getAllInfo();
        require_once 'Application/Views/' . $view . '.php';
    }
}
