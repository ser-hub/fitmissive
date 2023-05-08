<?php

namespace Application\Core;

use Application\Utilities\Redirect;
use Application\Services\{UserService, InfoService, ChatService};

abstract class Controller
{
    protected $loggedUser;
    protected $loggedUsername;
    protected $userService;
    protected $chatService;
    private $infoService = null;

    public function __construct()
    {
        $this->userService = UserService::getInstance();
        $this->infoService = InfoService::getInstance();
        $this->chatService = ChatService::getInstance();

        if (!$this->userService->isUserLoggedIn()) {
            Redirect::to('/index');
        }

        $loggedUserData = $this->userService->getLoggedUser(); 
        $this->loggedUsername = $loggedUserData->username;
        $this->loggedUser = $loggedUserData->user_id;
    }

    abstract public function index();

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
            $data['unseenMessages'] = $this->chatService->unseenMessages($this->loggedUser);
        }

        if ($this->infoService == null) {
            $this->infoService = InfoService::getInstance();
        }
        
        $data['info'] = [];
        $data['info'] = $this->infoService->getAllInfo();;
        require_once 'Application/Views/Common/header.php';
        require_once 'Application/Views/' . $view . '.php';
        require_once 'Application/Views/Common/footer.php';
        require_once 'Application/Views/Common/js/Socket/connect.php';

        if (file_exists('Application/Views/' . $view . '.js')) {
            echo '<script src="/Application/Views/' . $view . '.js" type="module"></script>';
        }
    }
}
