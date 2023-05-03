<?php

namespace Application\Services;

use Application\Repositories\{UserRepository, SplitRepository, ChatRepository, DCRepository, DeviceCookiesRepository};
use Application\Utilities\{Config, Constants, Session, Hash};

use \Exception;

class UserService
{
    private $splitRepository;
    private $userRepository;
    private $chatRepository;
    private $DCRepository;
    private $sessionName;
    private static $instance;

    private function __construct()
    {
        $this->userRepository = UserRepository::getInstance();
        $this->splitRepository = SplitRepository::getInstance();
        $this->chatRepository = ChatRepository::getInstance();
        $this->DCRepository = DeviceCookiesRepository::getInstance();

        $this->sessionName = Config::get('session/session_name');
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // User session service start
    public function register($user)
    {
        if (!$this->userRepository->addUser($user)) {
            throw new Exception('Something unexpected happened.');
        }
    }

    public function authenticate($user)
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
    // User session service end

    // Device cookie service start
    public function registerDeviceCookie($cookieContent)
    {
        $this->DCRepository->add($cookieContent);
    }

    public function isDeviceCookieLocked($cookieContent)
    {
        return $this->DCRepository->find($cookieContent) != false ? true : false;
    }

    private function lockOutDeviceCookie($cookieContent)
    {
        $deviceCookie = $this->DCRepository->find($cookieContent);

        if ($deviceCookie) {
            $this->DCRepository->delete($deviceCookie->dc_id);
        }
    }

    public function registerDeviceCookieFail($cookieContent)
    {
    }

    public function isLockedOutForUntrustedUsers($username)
    {
        $user = $this->getUser($username);
        if ($user) {
            return time() < $user->locked_untrusted_until;
        }
    }

    private function lockOutUntrustedUsers($username)
    {
        $this->updateUser([
            'locked_untrusted_until' => time() + 1800,
            'untrusted_failed_attempts' => 0
        ], $username);
    }

    public function registerRegularFail($username)
    {
        $user = $this->getUser($username);

        if ($user->untrusted_failed_attempts == 0 || time() > $user->initial_untrusted_failed_attempt + 1800) {
            $this->updateUser([
                'initial_untrusted_failed_attempt' => time(),
                'untrusted_failed_attempts' => 1
            ], $username);
        } elseif ($user->untrusted_failed_attempts < 10) {
            $this->updateUser([
                'untrusted_failed_attempts' => $user->untrusted_failed_attempts + 1
            ], $username);
        } elseif ($user->untrusted_failed_attempts == 10) {
            $this->lockOutUntrustedUsers($username);
        }
    }
    // Device cookie service end

    // General user service start
    public function getUser($user)
    {
        return $this->userRepository->find($user);
    }

    public function updateUser($fields = [], $username = null)
    {
        $id = null;
        if ($username == null && $this->isUserLoggedIn()) {
            $id = $this->getLoggedUser()->user_id;
        } else {
            $id = $this->userRepository->find($username)->user_id;
        }

        if (!$this->userRepository->updateUser($id, $fields)) {
            throw new Exception('Something unexpected happened.');
        }
    }

    public function deleteUser($username = null)
    {
        $user = $this->userRepository->find($username);
        if ($user) {
            if (
                !$this->splitRepository->deleteUserSplits($user->user_id) ||
                !$this->chatRepository->deleteChatsOf($user->user_id) ||
                !$this->userRepository->deleteAllFollows($user->user_id) ||
                !$this->userRepository->deleteUser($user->user_id)
            ) {
                throw new Exception('Something unexpected happened.');
            }
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
    // General user service end

    // User color service start
    public function getUserColor($userId)
    {
        return $this->userRepository->find($userId)->color_hex;
    }

    public function setLoggedUserColor($colorId)
    {
        $this->updateUser(['color_id' => $colorId], $this->getLoggedUser()->user_id);
    }
    // User color service end

    // User follows service start
    public function getFollowsArrayOf($user)
    {
        $results = $this->userRepository->getUserFollows($user);

        if ($results) {
            $followsArray = [];
            foreach ($results as $result) {
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
        $followed = $this->userRepository->find($followed)->user_id;
        if (
            $followed != $follower &&
            $this->getFollowsCountOf($follower) < 300 &&
            !$this->userRepository->addFollow($follower, $followed)
        ) {
            throw new Exception('Something unexpected happened.');
        }
    }

    public function unfollow($follower, $followed)
    {
        $followed = $this->userRepository->find($followed)->user_id;
        if (
            $followed != $follower &&
            !$this->userRepository->deleteFollow($follower, $followed)
        ) {
            throw new Exception('Something unexpected happened.');
        }
    }
    // User follows service end

    // User picture service start
    public function savePictureOf($username, $file)
    {
        $fileNameExploded = explode('.', $file['name']);
        $newExt = strtolower(end($fileNameExploded));
        $fileName = Hash::make($username);

        $picturePath = $this->getPicturePathOf($username);
        if ($picturePath !== Constants::DEFAULT_IMAGE) {
            unlink(substr($picturePath, 1));
        }

        $path = $this->getPicturePath($fileName, $newExt);
        if (move_uploaded_file($file['tmp_name'], substr($path, 1, strlen($path) - 1))) {
            return true;
        }
        return false;
    }

    private function getPicturePath($filename = null, $ext = null)
    {
        return $filename != null && $ext != null ? Constants::IMAGE_PATH . $filename . '.' . $ext : Constants::DEFAULT_IMAGE;
    }

    public function getPicturePathOf($username = null)
    {
        if ($username == null) {
            return $this->getPicturePath();
        }

        $fileName = Hash::make($username);
        foreach (Constants::ALLOWED_IMAGE_TYPES as $ext) {
            $path = $this->getPicturePath($fileName, $ext);
            if (file_exists(substr($path, 1, strlen($path) - 1))) {
                return $path;
            }
        }

        return $this->getPicturePath();
    }
    // User picture service end
}
