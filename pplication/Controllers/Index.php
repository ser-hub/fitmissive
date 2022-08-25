<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Services\UserService;
use Application\Utilities\{Redirect, Session, Validator, Input, Token};
use Application\Models\User;

use \Exception;

class Index extends Controller
{
    private $data;

    public function __construct()
    {
        $this->userService = UserService::getInstance();

        if ($this->userService->isUserLoggedIn()) {
            Redirect::to('/home');
        }
    }

    public function index()
    {
        if ($this->userService->isUserLoggedIn()) {
            Redirect::to('/home');
        }

        $this->view('home/index', $this->data);
    }

    public function loginAction()
    {
        if (Input::exists()) {
            if (Token::check(Input::get('LoginToken'), 'session/login_token')) {
                $data = [];

                if (!empty($_POST)) {
                    $data['LogInput'] = $_POST;
                }

                $validator = new Validator();
                $validator->check($_POST, array(
                    'username' => array(
                        'required' => true,
                    ),
                    'password' => array(
                        'required' => true,
                    )
                ));

                if ($validator->passed()) {
                    $user = array(
                        'username' => Input::get('username'),
                        'password' => Input::get('password')
                    );

                    if ($this->userService->login($user)) {
                        $this->index();
                        return;
                    } else {
                        $data['LogErrors'] = array('Username or password is incorrect.');
                    }
                } else {
                    $data['LogErrors'] = $validator->errors();
                }

                $this->data = $data;
            }
        }
        $this->index();
    }

    public function registerAction()
    {
        if (Input::exists()) {
            if (Token::check(Input::get('RegisterToken'), 'session/register_token')) {
                $data = [];

                if (!empty($_POST)) {
                    $data['RegInput'] = $_POST;
                }

                $validator = new Validator();
                $validator->check($_POST, array(
                    'usernameReg' => array(
                        'name' => 'Username',
                        'required' => true,
                        '!contains' => ' \\/?%&#@!*()+=,;:\'"',
                        'min' => 2,
                        'max' => 32,
                        'unique' => 'users',
                        'dbColumn' => 'username'
                    ),
                    'passwordReg' => array(
                        'name' => 'Password',
                        'required' => true,
                        'min' => 6,
                        'max' => 64
                    ),
                    'password2' => array(
                        'name' => 'Password',
                        'required' => true,
                        'matches' => 'passwordReg'
                    ),
                    'email' => array(
                        'name' => 'Email',
                        'required' => true,
                        'email' => true,
                        'unique' => 'users',
                        'dbColumn' => 'email'
                    )
                ));

                if ($validator->passed()) {
                    $user = new User(
                        Input::get('usernameReg'),
                        Input::get('email'),
                        Input::get('passwordReg')
                    );

                    try {
                        $this->userService->register($user);
                        Session::flash('success', 'You have been registered and can now log in.');
                    } catch (Exception $e) {
                        Session::flash('error', $e->getMessage());
                    }
                    unset($data['RegInput']);
                } else {
                    $data['RegErrors'] = $validator->errors();
                }

                $this->data = $data;
            }
        }
        $this->index();
    }
}
