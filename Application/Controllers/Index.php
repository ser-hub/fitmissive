<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Services\UserService;
use Application\Utilities\{Redirect, Session, Validator, Input, Token, Mailer, Hash};

class Index extends Controller
{
    private $data;

    public function __construct()
    {
        $this->userService = UserService::getInstance();
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
            if (Token::check(Input::get('login-token'), 'session/login_token')) {
                $data = [];

                $user = [
                    'username' => Input::get('username'),
                    'password' => Input::get('password')
                ];

                if ($this->userService->authenticate($user)) {
                    $this->index();
                    return;
                } else {
                    $data['logErrors'] = ['Грешно потребителско име или парола'];
                }

                $this->data = $data;
            }
        }
        $this->index();
    }

    public function registerAction()
    {
        if (Input::exists()) {
            if (Token::check(Input::get('register-token'), 'session/register_token')) {
                $data = [];

                $status = $this->userService->register($_POST);

                if (is_array($status)){
                    $data['regErrors'] = $status;
                } else {
                    if (!$status) {
                        $data['regErrors'] = ['Грешка при регистрирането ви. Опитайте отново.'];
                    } else {
                        Session::flash('success', 'Успешна регистрация.');
                    }
                }

                $this->data = $data;
            }
        }
        $this->index();
    }

    public function forgottenPassword()
    {
        if (Input::exists()) {
            echo $this->userService->initiatePR(Input::get('pr-target'));
        }
    }

    public function recoverPassword($PRKey = '')
    {
        if ($this->userService->validatePRKey($PRKey)) {
            $this->view('password-reset/index', [
                'key' => $PRKey
            ]);
        } else {
            $this->index();
        }
    }

    public function updatePassword()
    {
        $status = '';
        if (Input::exists()) {
            if (Token::check(Input::get('token'), 'session/pr_token')) {
                $status = $this->userService->finishPR(Input::get('key'), [
                    'password' => Input::get('password'), 
                    'password2' => input::get('password2')
                ]);
            }
        }
        $this->view('password-reset/index', [
            'key' => Input::get('key'),
            'status' => $status . '<a href="/index">Към начало</a>'
        ]);
    }
}
