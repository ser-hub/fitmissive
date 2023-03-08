<?php
namespace Application\Controllers;

use Application\Core\Controller;
use Application\Services\SplitService;
use Application\Utilities\{Constants, Input, Token, Validator, Redirect};

class profile extends Controller
{
    private $loggedUserRole;
    private $splitService;
    private $data;

    public function __construct()
    {
        $this->splitService = SplitService::getInstance();

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

        $this->view('profile', [
            'isAdmin' => $isAdmin,
            'user' => $user,
            'isMyProfile' => $this->loggedUser == $user->user_id,
            'picturePath' => $this->userService->getPicturePathOf($user->username),
            'data' => $this->data,
            'splits' => $this->splitService->splitsOf($user->user_id),
            'follows' => $this->userService->getFollowsCountOf($user->user_id), //unify
            'followers' => $this->userService->getFollowersCountOf($user->user_id),
            'ratings' => $this->splitService->getRatingsCountOf($user->user_id),
            'rating' => $this->splitService->getRating($this->loggedUser, $user->user_id),
            'isFollowing' => $loggedUserFollowsArray ?
                in_array($user->user_id, $this->userService->getFollowsArrayOf($this->loggedUser)) : false
        ]);
    }

    public function updateSplit($username, $day)
    {
        if (Input::exists()) {
            if (Token::check(Input::get('token'), 'session/weekday_tokens/' . $day)) {
                $data = [];

                if (!empty($_POST)) {
                    $data['addInput'] = $_POST;
                }
                $data['addInput']['day'] = $day;

                $validator = new Validator();
                $validator->check($_POST, array(
                    'title' => array(
                        'name' => 'Title',
                        'required' => true,
                        'max' => 45
                    ),
                    'description' => array(
                        'name' => 'Description',
                        'required' => true,
                        'max' => 800
                    )
                ));

                if ($validator->passed()) {
                    if (Input::keyExists('isEdit')) {
                        $this->splitService->updateSplit($day, $username, array(
                            'title' => Input::get('title'),
                            'description' => trim(Input::get('description'))
                        ));
                    } else {
                        $this->splitService->addSplit($username, $day, array(
                            'title' => Input::get('title'),
                            'description' => trim(Input::get('description'))
                        ));
                    }
                } else {
                    $data['addErrors'] = $validator->errors();
                }
                
                $this->data = $data;
            }
        }
            
        $this->index($username);     
    }

    public function updateUser($username)
    {
        if (Input::exists()) {
            if (Token::check(Input::get('token'), 'session/profile_edit_token')) {
                $data = [];

                $validator = new Validator();
                $validator->check($_POST, [
                    'fullname' => [
                        'name' => 'fullname',
                        '!contains' => '\\/?%&#@!*()+=,;:\'"',
                        'min' => 2,
                        'max' => 32,
                    ],
                    'description' => [
                        'name' => 'desciption',
                        'max' => 500,
                    ]

                ]);
                if ($validator->passed()) {
                    $this->userService->updateUser(array(
                        'fullname' => Input::get('fullname'),
                        'description' => Input::get('description'),
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

    public function rate($username) {
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
            }
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