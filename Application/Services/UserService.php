<?php

namespace Application\Services;

use Application\Repositories\{UserRepository, SplitRepository, ChatRepository};
use Application\Utilities\{Config, Constants, Session, Hash, Validator, Mailer};
use Application\Models\User;

use \Exception;

class UserService
{
    private $splitRepository;
    private $userRepository;
    private $chatRepository;
    private $sessionName;
    private static $instance;

    private function __construct()
    {
        $this->userRepository = UserRepository::getInstance();
        $this->splitRepository = SplitRepository::getInstance();
        $this->chatRepository = ChatRepository::getInstance();

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
    public function register($input)
    {
        $errors = [];
        $validator = new Validator();
        $validator->check($input, [
            'username-reg' => [
                'name' => 'Потребилеското име',
                'required' => true,
                '!contains' => ' \\/?%&#@!*()+=,;:\'"',
                'min' => 2,
                'max' => 32,
            ],
            'password-reg' => [
                'name' => 'Паролата',
                'required' => true,
                'min' => 6,
                'max' => 64
            ],
            'password2' => [
                'name' => 'Втората парола',
                'required' => true,
                'matches' => 'password-reg'
            ],
            'email' => [
                'name' => 'Имейлът',
                'required' => true,
                'email' => true,
                'max' => 255
            ]
        ]);

        if (!$validator->passed()) {
            $errors = $validator->errors();
        }

        if ($this->getUser($input['username-reg'])) {
            $errors[] = 'Потребителското име е заето.';
        }
        if ($this->emailExists($input['email'])) {
            $errors[] = 'Имейлът е зает.';
        }

        if (!empty($errors)) {
            return $errors;
        } else {
            $user = new User(
                $input['username-reg'],
                $input['email'],
                $input['password-reg']
            );
            if (!$this->userRepository->addUser($user)) {
                return false;
            }
            return true;
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

    // General user service start
    public function getUser($user)
    {
        return $this->userRepository->find($user);
    }

    public function getUserIdByEmail($email)
    {
        return $this->userRepository->getUserByX('email', $email);
    }

    public function updateUser($fields = [], $username = null)
    {
        $validator = new Validator();
        $validator->check($fields, [
            'fullname' => [
                'name' => 'Пълното име',
                '!contains' => '0123456789\\/?%&#@!*()+=,;:\'"',
                'min' => 2,
                'max' => 32,
            ],
            'description' => [
                'name' => 'Описанието',
                'max' => 500,
            ],
            'email' => [
                'name' => 'Имейлът',
                'required' => true,
                'email' => true,
                'max' => 255
            ]
        ]);

        if (!$validator->passed()) {
            return $validator->errors();
        }

        $id = null;
        if ($username == null && $this->isUserLoggedIn()) {
            $id = $this->getLoggedUser()->user_id;
            $email = $this->getLoggedUser()->email;
        } else {
            $id = $this->userRepository->find($username)->user_id;
            $email = $this->userRepository->find($username)->email;
        }

        if (strcmp($fields['email'], $email) != 0) {
            if ($this->emailExists($fields['email'])) {
                return ['Имейлът вече съществува.'];
            }
        } else {
            unset($fields['email']);
        }

        return $this->userRepository->updateUser($id, $fields);
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

    public function emailExists($email)
    {
        return $this->userRepository->getUserByX('email', $email) != false;
    }
    // General user service end

    // Fogotten password service start
    public function initiatePR($target)
    {
        $targetUser = $this->getUserIdByEmail($target);
        if ($targetUser) {
            $FPKey = Hash::salt(8);

            if (!$this->userRepository->updateUser($targetUser->user_id, [
                'pr_key' => $FPKey,
                'pr_key_sent_at' => time()
            ])) {
                return 'Грешка. Опитайте отново.';
            } elseif (Mailer::sendPasswordRecoveryMail($target, $FPKey)) {
                return 'Беше ви изпратен имейл с линк за смяна на паролата ви';
            } else {
                return 'Грешка при изпращането на имейл';
            }
        } else {
            return 'Не беше намерен потребител с този имейл';
        }
    }

    public function finishPR($key, $input)
    {
        if (!$this->validatePRKey($key)) {
            return 'Този линк е невалиден или изтекъл';
        }

        $validator = new Validator();
        $validator->check($input, [
            'password' => [
                'name' => 'Паролата',
                'required' => true,
                'min' => 6,
                'max' => 64
            ],
            'password2' => [
                'name' => 'Втората парола',
                'required' => true,
                'matches' => 'password'
            ]
        ]);

        if (!$validator->passed()) {
            return $validator->errors()[0];
        }

        $targetUser = $this->userRepository->getUserByX('pr_key', $key);
        if ($this->userRepository->updateUser($targetUser->user_id, [
            'pr_key' => null,
            'pr_key_sent_at' => null
        ])) {
            return 'Успешна смяна на паролата!';
        } else {
            return 'Грешка при смяна на паролата';
        }
    }

    public function validatePRKey($key)
    {
        $targetUser = $this->userRepository->getUserByX('pr_key', $key);
        return $targetUser && $targetUser->pr_key_sent_at < time() + 3600;
    }

    public function updateUserPassword($PRKey, $newPassword)
    {
        $targetUser = $this->userRepository->getUserByX('pr_key', $PRKey);
        if ($this->validatePRKey($PRKey)) {
            $salt = Hash::salt(16);
            $passwordHashed = Hash::make($newPassword, $salt);
            return $this->updateUser([
                'salt' => $salt,
                'password' => $passwordHashed
            ], $targetUser->user_id);
        }
    }
    // Forgotten password service end

    // User color service start
    public function getUserColor($userId)
    {
        return $this->userRepository->find($userId)->color_hex;
    }

    public function setLoggedUserColor($colorId)
    {
        $this->userRepository->updateUser($this->getLoggedUser()->user_id, ['color_id' => $colorId]);
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
        $validator = new Validator();
        $validator->checkFile($file, [
            'allowedTypes' => Constants::ALLOWED_IMAGE_TYPES,
            'maxSize' => 5242880,
            'illegalSymbols' => [
                '.php',
            ]
        ]);

        if (!$validator->passed()) {
            return $validator->errors();
        }

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
