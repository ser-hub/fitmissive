<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Services\UserService;
use Application\Utilities\{Redirect, Session, Validator, Input, Token, DeviceCookies};
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

                $cookieFlag = false;
                /*
                if (!isset($_COOKIE['device_cookie'])) {
                    $deviceCookie = $_COOKIE['device_cookie'];
                    if (!DeviceCookies::validate(Input::get('username'), $deviceCookie)) {
                        if ($this->userService->isUserLockedOut(Input::get('username'))) {
                            $cookieFlag = true;
                        }
                    } else {
                        // if device cookie is in the lockout list- reject authentication
                    }
                } 

                if ($cookieFlag) {
                    $data['LogErrors'] = array("The account you're trying to access is temporarily locked.");
                    $this->data = $data;
                    $this->index();
                    return;
                }
                */

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
                        setcookie('device_cookie', DeviceCookies::generate($user['username']));
                        $this->index();
                        return;
                    } else {
                        $data['LogErrors'] = array('Username or password is incorrect.');
                        //process failed attempt
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
                $validator->check($_POST, [
                    'usernameReg' => [
                        'name' => 'Username',
                        'required' => true,
                        '!contains' => ' \\/?%&#@!*()+=,;:\'"',
                        'min' => 2,
                        'max' => 32,
                        'unique' => 'users',
                        'dbColumn' => 'username'
                    ],
                    'passwordReg' => [
                        'name' => 'Password',
                        'required' => true,
                        'min' => 6,
                        'max' => 64
                    ],
                    'password2' => [
                        'name' => 'Password',
                        'required' => true,
                        'matches' => 'passwordReg'
                    ],
                    'email' => [
                        'name' => 'Email',
                        'required' => true,
                        'email' => true,
                        'unique' => 'users',
                        'dbColumn' => 'email'
                    ]
                ]);

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