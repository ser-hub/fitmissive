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

    public function getUser($user)
    {
        return $this->userRepository->find($user);
    }

    public function updateUser($fields = [], $id = null)
    {
        if (!$id && $this->isUserLoggedIn()) {
            $id = $this->getLoggedUser()->user_id;
        }

        if (!$this->userRepository->updateUser($id, $fields)) {
            throw new Exception('Something unexpected happened.');
        }
    }

    public function searchUsers($logged, $keyword = null)
    {
        $results = $this->userRepository->getAllUsersLike($keyword);

        if ($results) {
            foreach($results as $key => $result) {
                if ($result->user_id === $logged) {
                    unset($results[$key]);
                }
            }
        }

        return $results;
    }

    public function getFollowsArrayOf($user)
    {
        $results = $this->userRepository->getUserFollows($user);

        if ($results) {
            $followsArray = [];
            foreach($results as $result) {
                $followsArray[] = $result->followed_id;
            }
            return $followsArray;
        }
        return $results;
    }

    public function getFollowsCountOf($userId)
    {
        $follows = $this->getFollowsArrayOf($userId);
        return $follows ? count($follows) : 0;
    }

    public function getFollowersCountOf($userId)
    {
        $followers = $this->userRepository->getUserFollowers($userId);
        return $followers ? count($followers) : 0;
    }

    public function follow($follower, $followed)
    {
        if (!$this->userRepository->addFollow($follower, $followed)) {
            throw new Exception('Something unexpected happened.');
        }
    }

    public function unfollow($follower, $followed)
    {
        if (!$this->userRepository->deleteFollow($follower, $followed)) {
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
