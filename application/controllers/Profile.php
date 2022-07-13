<?php
namespace Application\Controllers;

use Application\Core\Controller;
use Application\Services\{UserService, SplitService};
use Application\Utilities\{Input, Token, Validate, Redirect};

class profile extends Controller
{
    private $loggedUser = null;
    private $userService;
    private $splitService;

    public function __construct()
    {
        $this->userService = UserService::getInstance();
        $this->splitService = SplitService::getInstance();

        if (!$this->userService->isUserLoggedIn()) {
            Redirect::to('/index');
        }

        $this->loggedUser = $this->userService->getLoggedUser()->user_id;
    }
    
    public function index($username = null)
    {
        $user = null;
        if ($username == null) {
            $user = $this->userService->getUser($this->loggedUser);
        } else {
            $user = $this->userService->getUser($username);
        }   

        $loggedUserFollowsArray = $this->userService->getFollowsArrayOf($this->loggedUser);

        $this->view('profile', array(
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'splits' => $this->splitService->splitsOf($user->user_id),
            'follows' => $this->userService->getFollowsCountOf($user->user_id),
            'followers' => $this->userService->getFollowersCountOf($user->user_id),
            'isFollowing' => $loggedUserFollowsArray ? 
                in_array($user->user_id, $this->userService->getFollowsArrayOf($this->loggedUser)) : false
        ));
    }

    public function updateSplit($day)
    {
        if (Input::exists()) {
            if (Token::check(Input::get('token'), 'session/weekday_tokens/' . $day)) {
                $data = [];

                if (!empty($_POST)) {
                    $data['addInput'] = $_POST;
                }
                $data['addInput']['day'] = $day;

                $validate = new Validate();
                $validate->check($_POST, array(
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

                if ($validate->passed()) {
                    if (Input::keyExists('isEdit')) {
                        $this->splitService->updateSplit($day, $this->loggedUser, array(
                            'title' => Input::get('title'),
                            'description' => trim(Input::get('description'))
                        ));
                    } else {
                        $this->splitService->addSplit($this->loggedUser, $day, array(
                            'title' => Input::get('title'),
                            'description' => trim(Input::get('description'))
                        ));
                    }
                } else {
                    $data['addErrors'] = $validate->errors();
                }
                $this->view('profile', array(
                    'splits' => $this->splitService->splitsOf($this->loggedUser),
                    'loggedUser' => $this->loggedUser,
                    'data' => $data,
                    'user' => $this->userService->getUser($this->loggedUser),
                    'follows' => $this->userService->getFollowsCountOf($this->loggedUser),
                    'followers' => $this->userService->getFollowersCountOf($this->loggedUser),
                ));
                return;
            }
        }
        $this->index();
    }

    public function updateUser()
    {
        if (Input::exists()) {
            if (Token::check(Input::get('token'), 'session/profile_edit_token')) {
                $this->userService->updateUser(array(
                    'fullname' => Input::get('fullname'),
                    'description' => Input::get('description')
                ));
            }
        }
        $this->index();
    }

    public function follow()
    {
        if (Input::exists()) {
            if (Input::get('action') == 'Follow') {
                $this->userService->follow($this->loggedUser, Input::get('userId'));
            }
            else if (Input::get('action') == 'Unfollow') {
                $this->userService->unfollow($this->loggedUser, Input::get('userId'));
            }
        }
        $this->index(Input::get('username'));
    }
}