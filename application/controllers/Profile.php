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

        $this->view('profile', array(
            'isAdmin' => $isAdmin,
            'user' => $user,
            'picturePath' => $this->userService->getPicturePathOf($user->user_id),
            'data' => $this->data,
            'splits' => $this->splitService->splitsOf($user->user_id),
            'follows' => $this->userService->getFollowsCountOf($user->user_id),
            'followers' => $this->userService->getFollowersCountOf($user->user_id),
            'isFollowing' => $loggedUserFollowsArray ?
                in_array($user->user_id, $this->userService->getFollowsArrayOf($this->loggedUser)) : false
        ));
    }

    public function updateSplit($day)
    {
        $username = '';
        if (Input::exists()) {
            if (Token::check(Input::get('token'), 'session/weekday_tokens/' . $day)) {
                $data = [];
                $username = Input::get('username');

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
                        $this->splitService->updateSplit($day, Input::get('user_id'), array(
                            'title' => Input::get('title'),
                            'description' => trim(Input::get('description'))
                        ));
                    } else {
                        $this->splitService->addSplit(Input::get('user_id'), $day, array(
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
        if(isset($this->data['addErrors'])) {
            $this->index($username);
        } else {
        Redirect::to('/profile/' . $username);
        }
    }

    public function updateUser()
    {
        $username = '';
        if (Input::exists()) {
            if (Token::check(Input::get('token'), 'session/profile_edit_token')) {
                $username = Input::get('username');
                $fullname = Input::get('fullname');
                $description = Input::get('description');
                $data = [];

                if (strlen($fullname) > 64) {
                    $data['uploadErrors'] = 'Name is too long';
                } elseif (strlen($description) > 800) {
                    $data['uploadErrors'] = 'Description is too long';
                } else {
                    $this->userService->updateUser(array(
                        'fullname' => $fullname,
                        'description' => $description,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ), Input::get('user_id'));
                }

                if (Input::keyExists('profilePic') && Input::get('profilePic')['name'] != '') {

                    $validator = new Validator();
                    $validator->checkFile(Input::get('profilePic'), array(
                        'allowedTypes' => Constants::ALLOWED_IMAGE_TYPES,
                        'maxSize' => 5242880,
                        'illegalSymbols' => array(
                            '.php',
                        )
                    ));

                    if ($validator->passed()) {
                        if (!$this->userService->savePictureOf(
                            Input::get('user_id'), 
                            Input::get('profilePic'))) {
                            $data['uploadErrors'] = array('Error saving the file.');
                        }

                    } else {
                        $data['uploadErrors'] = $validator->errors();
                        $data['edit'] = true;
                    } 
                
                $this->data = $data;
                }
            }
        }
        if(isset($this->data['uploadErrors'])) {
            $this->index($username);
        } else {
        Redirect::to('/profile/' . $username);
        }
    }

    public function follow()
    {
        $username = '';
        if (Input::exists()) {
            $username = Input::get('username');

            if (Input::get('action') == 'Follow') {
                $this->userService->follow($this->loggedUser, Input::get('userId'));
            }
            else if (Input::get('action') == 'Unfollow') {
                $this->userService->unfollow($this->loggedUser, Input::get('userId'));
            }
        }
        $this->index($username);
    }

    public function delete()
    {
        if ($this->loggedUserRole == Constants::USER_ROLE_ADMIN) {
            if (Input::exists() && Token::check(Input::get('token'), 'session/profile_delete_token')) {
                $this->userService->deleteUser(Input::get('user_id'));
            }
        }

        Redirect::to('/home');
    }
}