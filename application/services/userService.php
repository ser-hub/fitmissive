<?php

namespace Application\Services;

use Application\Repositories\UserRepository;
use Application\Utilities\{Config, Session, Hash};

use \Exception;

class UserService
{
    private $userRepository;
    private $sessionName;
    private static $instance;

    private function __construct()
    {;
        $this->userRepository = UserRepository::getInstance();

        $this->sessionName = Config::get('session/session_name');
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function register($user)
    {
        if (!$this->userRepository->addUser($user)) {
            throw new Exception('Something unexpected happened.');
        }
    }

    public function login($user)
    {
        $data = $this->userRepository->find($user['username']);
        if ($data) {
            if ($data->password === Hash::make($user['password'], $data->salt)) {
                Session::put($this->sessionName, $data->user_id);
                return true;
            }
        }
        return false;
    }

    public function updateUser($fields = [], $id = null)
    {
        if (!$id && $this->isUserLoggedIn()) {
            $id = Session::get($this->sessionName);
        }

        if (!$this->userRepository->updateUser($id, $fields)) {
            throw new Exception('Something unexpected happened.');
        }
    }

    public function getLoggedUser()
    {
        return $this->userRepository->find(Session::get($this->sessionName));
    }

    public function isUserLoggedIn()
    {
        return Session::exists($this->sessionName);
    }

    public function logout()
    {
        Session::delete($this->sessionName);
    }
}
