<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Services\{UserService, SplitService};
use Application\Utilities\{Redirect, Input, Token, Validate};

class Home extends Controller
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

    public function index()
    {
        if ($this->loggedUser) {
            $this->view('home/home', array(
                'loggedUser' => $this->loggedUser,
                'splits' => $this->splitService->splitsOf($this->loggedUser)
            ));
        } else {
            echo '<i>Error getting logged user</i>';
        }
    }

    public function update($day)
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
                $this->view('home/home', array(
                    'splits' => $this->splitService->splitsOf($this->loggedUser),
                    'loggedUser' => $this->loggedUser,
                    'data' => $data,
                ));
                return;
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
