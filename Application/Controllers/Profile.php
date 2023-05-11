<?php
namespace Application\Controllers;

use Application\Core\Controller;
use Application\Services\{SplitService, ColorService};
use Application\Utilities\{Constants, Input, Token, Validator, Redirect};

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
            'splits' => $this->splitService->splitsOf($user->user_id),
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

                $validator = new Validator();
                $validator->check($_POST, [
                    'fullname' => [
                        'name' => 'Пълното име',
                        '!contains' => '\\/?%&#@!*()+=,;:\'"',
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
                        'unique' => 'users',
                        'dbColumn' => 'email',
                        'max' => 255
                    ]

                ]);
                if ($validator->passed()) {
                    $this->userService->updateUser(array(
                        'fullname' => Input::get('fullname'),
                        'description' => Input::get('description'),
                        'email' => Input::get('email'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ), $username);
                } else {
                    $data['uploadErrors'] = $validator->errors();
                }

                if (Input::keyExists('profilePic') && Input::get('profilePic')['name'] != '') {

                    $validator = new Validator();
                    $validator->checkFile(Input::get('profilePic'), [
                        'allowedTypes' => Constants::ALLOWED_IMAGE_TYPES,
                        'maxSize' => 5242880,
                        'illegalSymbols' => [
                            '.php',
                        ]
                    ]);

                    if ($validator->passed()) {
                        if (!$this->userService->savePictureOf(
                            $username, 
                            Input::get('profilePic'))) {
                            $data['uploadErrors'] = ['Error saving the file.'];
                        }

                    } else {
                        $data['uploadErrors'] = $validator->errors();
                        $data['edit'] = true;
                    } 
                }
                $this->data = $data;
            }
        }
        if(isset($this->data['uploadErrors'])) {
            $this->index($username);
        } else {
        Redirect::to('/profile/' . $username);
        }
    }

    public function rate($username = null) {
        if (Input::exists() && Token::check(Input::get('token'), 'session/rating_token')) {
            if (Input::keyExists('rating')) {
                $response = ['token' => Token::generate('session/rating_token')];
                $user = $this->userService->getUser($username);
                if (!$user) {
                    $response['result'] = 'Error';
                    return;
                }

                if ($this->splitService->getRating($this->loggedUser, $user->user_id) === null) {
                    if ($this->splitService->rate($this->loggedUser, $username, Input::get('rating'))) {
                        $response['result'] = 'Rated';
                    } else {
                        $response['result'] = 'Error';
                    }
                }
                else if ($this->splitService->getRating($this->loggedUser, $user->user_id) != Input::get('rating')){
                    if (!$this->splitService->updateRating($this->loggedUser, $user->user_id, Input::get('rating'))) {
                        $response['result'] = 'Updated';
                    } else {
                        $response['result'] = 'Error';
                    }
                }
                else if ($this->splitService->getRating($this->loggedUser, $user->user_id) === Input::get('rating')) {
                    return;
                } else {
                    $response['result'] = 'Error';
                }
                echo json_encode($response);
                return;
            }
        }
        Redirect::to('/home');
    }

    public function updateColor() {
        if (Input::exists() && Token::check(Input::get('token'), 'session/color_token')) {
            if (Input::keyExists('value')) {
                $result = false;
                $colorId = $this->colorService->getColorId(Input::get('value'));
                if ($colorId) {
                    $this->userService->setLoggedUserColor($colorId);
                    $result = true;
                }
                echo json_encode([
                    'token' => Token::generate('session/color_token'),
                    'result' => $result
                ]);
                return;
            }
        }
        Redirect::to('/home');
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