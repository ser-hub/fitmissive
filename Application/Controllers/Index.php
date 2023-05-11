<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Services\UserService;
use Application\Utilities\{Redirect, Session, Validator, Input, Token, DeviceCookies, Mailer, Hash};
use Application\Models\User;
use \Exception;

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
            if (Token::check(Input::get('LoginToken'), 'session/login_token')) {
                $data = [];

                if (!empty($_POST)) {
                    $data['LogInput'] = $_POST;
                }

                $user = [
                    'username' => Input::get('username'),
                    'password' => Input::get('password')
                ];

                /*
                $lockedError = ['Този акаунт беше временно заключен от съображения за сигурност'];
                $credentialsError = ['Грешно потребителско име или парола'];
                if (isset($_COOKIE['device_cookie']))) {
                    $deviceCookieContent = $_COOKIE['device_cookie']
                    if ($this->userService->validateCookie($user['username'], $deviceCookieContent)) {
                        if (!$this->userService->isDeviceCookieLocked($user['username'])) {
                            $data['LogErrors'] = $lockedError;
                        }
                        elseif ($this->userService->authenticate($user)) {
                            // issue device cookie
                            issueCookie($user['username']);
                            $this->index();
                            return; 
                        } else {
                            $data['LogErrors'] = credentialsError;
                            //register cookie failed attempt
                            $this->userService->registerCookieFail($user['username']));
                        }
                    }
                }

                if ($this->userService->isLockedForUntrustedUsers($user['username'])) {
                        $data['LogErrors'] = $lockedError;
                } else {
                    if ($this->userService->authenticate($user)) {
                        // issue device cookie
                        issueCookie($user['username']);
                        $this->index();
                        return; 
                    } else {
                        $data['LogErrors'] = credentialsError;
                        //register regular failed attempt
                        $this->userService->registerRegularFail($deviceCookie));
                    }
                }
                */

                if ($this->userService->authenticate($user)) {
                    // issue device cookie
                    setcookie('device_cookie', DeviceCookies::generate($user['username']));
                    $this->index();
                    return;
                } else {
                    $data['LogErrors'] = ['Грешно потребителско име или парола'];
                    //process failed attempt
                }

                $this->data = $data;
            }
        }
        $this->index();
    }

    private function issueCookie($username)
    {
        $deviceCookieContent = DeviceCookies::generate($username);
        $this->userService->registerDeviceCookie($username, $deviceCookieContent);
        setcookie('device_cookie', $deviceCookieContent);
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
                        'name' => 'Потребилеското име',
                        'required' => true,
                        '!contains' => ' \\/?%&#@!*()+=,;:\'"',
                        'min' => 2,
                        'max' => 32,
                        'unique' => 'users',
                        'dbColumn' => 'username'
                    ],
                    'passwordReg' => [
                        'name' => 'Паролата',
                        'required' => true,
                        'min' => 6,
                        'max' => 64
                    ],
                    'password2' => [
                        'name' => 'Втората парола',
                        'required' => true,
                        'matches' => 'passwordReg'
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
                    $user = new User(
                        Input::get('usernameReg'),
                        Input::get('email'),
                        Input::get('passwordReg')
                    );

                    try {
                        $this->userService->register($user);
                        Session::flash('success', 'Успешна регистрация.');
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

    public function forgottenPassword()
    {
        if (Input::exists()) {
            $result = '';

            $targetUser = $this->userService->getUserIdByEmail(Input::get('pr-target'));
            if ($targetUser) {
                $FPKey = Hash::salt(8);
                if (Mailer::sendPasswordRecoveryMail(Input::get('pr-target'), $FPKey)) {
                    $result = 'Беше ви изпратен имейл с линк за смяна на паролата ви';
                    $this->userService->initiatePR($targetUser->user_id, $FPKey);
                } else {
                    $result = 'Грешка при изпращането на имейл';
                }
            } else {
                $result = 'Не беше намерен потребител с този имейл';
            }

            echo $result;
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
        if (Input::exists()) {
            if (Token::check(Input::get('token'), 'session/pr_token')) {
                $status = 'Успешна смяна на паролата!';
                if (!$this->userService->validatePRKey(Input::get('key'))) {
                    $status = 'Този линк е невалиден или изтекъл';
                }
                $validator = new Validator();
                $validator->check($_POST, [
                    'password' => [
                        'name' => 'Паролата',
                        'required' => true,
                        'min' => 6,
                        'max' => 64
                    ],
                    'password2' => [
                        'name' => 'Втората парола',
                        'required' => true,
                        'matches' => 'password'
                    ]
                ]);

                if ($validator->passed()) {
                    $this->userService->updateUserPassword(
                        Input::get('key'),
                        input::get('password')
                    );
                    $this->userService->finishPR(Input::get('key'));
                } else {
                    $status = $validator->errors()[0];
                }
            }
        }
        $this->view('password-reset/index', [
            'key' => Input::get('key'),
            'status' => $status . '<a href="/index">Към начало</a>'
        ]);
    }
}
