<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Services\SplitService;
use Application\Utilities\{Redirect, Input, Token, Validator, Constants};

class Home extends Controller
{
    private $splitService;
    private $data;

    public function __construct()
    {
        $this->splitService = SplitService::getInstance();

        parent::__construct();
    }

    public function index()
    {
        $followedSplits = $this->splitService->getRandomisedFollowedSplitsOf(
            $this->userService->getFollowsArrayOf($this->loggedUser));

        if (!empty($followedSplits)) {
            foreach($followedSplits as $split) {
                $user = $this->userService->getUser($split->user_id);

                if (isset($user->fullname)) {
                    $split->user_id = $user->fullname;
                } else {
                    $split->user_id = '@' . $user->username;
                }
            }
        }

        $this->view('home/home', array(
            'splits' => $this->splitService->splitsOf($this->loggedUser),
            'followedSplits' => $followedSplits,
            'data' => $this->data
        ));
    }

    public function update($day)
    {
        if (Input::exists()) {
            if (Token::check(Input::get('token'), 'session/weekday_tokens/' . $day)) {
                $data = [];

                if (!empty($_POST)) {
                    $data['updateInput'] = $_POST;
                }
                
                $data['updateInput']['day'] = $day;

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
                    $data['updateErrors'] = $validator->errors();
                }
                $this->data = $data;
            }
        }
        $this->index();
    }

    public function logout()
    {
        $this->userService->logout();
        Redirect::to('/index');
    }
}
