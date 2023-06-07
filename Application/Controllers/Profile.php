<?php
namespace Application\Controllers;

use Application\Core\Controller;
use Application\Services\{SplitService, ColorService};
use Application\Utilities\{Constants, Input, Token, Redirect};

class Profile extends Controller
{
    private $loggedUserRole;
    private $splitService;
    private $colorService;
    private $data;

    public function __construct()
    {
        $this->splitService = SplitService::getInstance();
        $this->colorService = ColorService::getInstance();

        parent::__construct();
        $this->loggedUserRole = $this->userService->getLoggedUser()->role_name;
    }
    
    public function index($username = null)
    {
        $user = null;
        if ($username == null) {
            $user = $this->userService->getUser($this->loggedUser);
        } else {
            $user = $this->userService->getUser($username);

            if (!$user){
                $user = $this->userService->getUser($this->loggedUser);
            }
        }

        $isAdmin = $this->loggedUserRole === Constants::USER_ROLE_ADMIN;
        
        $loggedUserFollowsArray = $this->userService->getFollowsArrayOf($this->loggedUser);

        $this->view('profile/profile', [
            'isAdmin' => $isAdmin,
            'user' => $user,
            'isMyProfile' => $this->loggedUser == $user->user_id,
            'picturePath' => $this->userService->getPicturePathOf($user->username),
            'data' => $this->data,
            'color' => $this->userService->getUserColor($user->username),
            'colors' => $this->colorService->getAllColorsHex(),
            'workout' => $this->splitService->splitsOf($user->user_id),
            'follows' => $this->userService->getFollowsCountOf($user->user_id), 
            'followers' => $this->userService->getFollowersCountOf($user->user_id),
            'ratings' => $this->splitService->getRatingsCountOf($user->user_id),
            'rating' => $this->splitService->getRating($this->loggedUser, $user->user_id),
            'isFollowing' => $loggedUserFollowsArray ?
                in_array($user->user_id, $this->userService->getFollowsArrayOf($this->loggedUser)) : false
        ]);
    }

    public function updateUser($username)
    {
        if (Input::exists()) {
            if (Token::check(Input::get('token'), 'session/profile_edit_token')) {
                $data = [];

                $status = $this->userService->updateUser([
                    'fullname' => Input::get('fullname'),
                    'description' => Input::get('description'),
                    'email' => Input::get('email'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ], $username);

                if (is_array($status)) {
                    $data['uploadErrors'] = $status;
                } else {
                    if (!$status) {
                        $data['uploadErrors'] = 'Грешка при актуализиране на данните ви.';
                    }
                }

                if (Input::keyExists('profilePic') && Input::get('profilePic')['name'] != '') {
                    $status = $this->userService->savePictureOf($username, Input::get('profilePic'));

                    if (is_array($status)) {
                        $data['uploadErrors'] = $status;
                    } else {
                        if (!$status) {
                            $data['uploadErrors'] = ['Грешка при запазването на снимката ви.'];
                        }
                    }
                }
                $this->data = $data;
            }
        }

        if(!empty($this->data['uploadErrors'])) {
            $this->data['edit'] = true;
            $this->index($username);
        } else {
            Redirect::to('/profile/' . $username);
        }
    }

    public function delete($username)
    {
        if ($this->loggedUserRole == Constants::USER_ROLE_ADMIN) {
            if (Input::exists() && Token::check(Input::get('token'), 'session/profile_delete_token')) {
                $this->userService->deleteUser($username);
            }
        }

        Redirect::to('/home');
    }
}