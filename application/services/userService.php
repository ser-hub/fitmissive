<?php

namespace Application\Services;

use Application\Repositories\{UserRepository, SplitRepository};
use Application\Utilities\{Config, Constants, Session, Hash};

use \Exception;

class UserService
{
    private $splitRepository;
    private $userRepository;
    private $sessionName;
    private static $instance;

    private function __construct()
    {
        $this->userRepository = UserRepository::getInstance();
        $this->splitRepository = SplitRepository::getInstance();

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

    public function getLoggedUserRole()
    {
        
    }

    public function updateUser($fields = [], $id = null)
    {
        if ($id == null && $this->isUserLoggedIn()) {
            $id = $this->getLoggedUser()->user_id;
        }

        if (!$this->userRepository->updateUser($id, $fields)) {
            throw new Exception('Something unexpected happened.');
        }
    }

    public function deleteUser($id = null)
    {
        if (!$this->splitRepository->deleteUserSplits($id) || !$this->userRepository->deleteUser($id)) {
            throw new Exception('Something unexpected happened.');
        }
    }

    public function searchUsers($keyword = null, $from = null, $count = null)
    {
        return $this->userRepository->getAllUsersLike(
            $keyword,
            $this->getLoggedUser()->user_id, 
            $from, 
            $count
        );
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

    public function savePictureOf($userId, $file)
    {
        $fileNameExploded = explode('.', $file['name']);
        $newExt = strtolower(end($fileNameExploded));
        $fileName = Hash::make($userId);

        if ($this->getPicturePathOf($userId) !== Constants::DEFAULT_IMAGE) {
            unlink($this->getPicturePathOf($userId));
        }

        if (move_uploaded_file($file['tmp_name'], $this->getPicturePath($fileName, $newExt))) {
            return true;
        }
        return false;
    }

    private function getPicturePath($filename = null, $ext = null)
    {
        return $filename != null ? Constants::IMAGE_PATH . $filename . '.' . $ext : Constants::DEFAULT_IMAGE;
    }

    public function getPicturePathOf($userId = null)
    {
        if ($userId == null) {
            return $this->getPicturePath();
        }

        $fileName = Hash::make($userId);
        foreach (Constants::ALLOWED_IMAGE_TYPES as $ext) {
            if (file_exists($this->getPicturePath($fileName, $ext))) {
                return $this->getPicturePath($fileName, $ext);
            }
        }

        return $this->getPicturePath();
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
