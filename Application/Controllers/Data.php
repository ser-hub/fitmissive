<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Services\{ColorService, ExerciseService, SplitService};
use Application\Utilities\{Input, Token, Redirect, Constants};

class Data extends Controller
{
    private $exerciseService,
        $splitService,
        $colorService;

    public function __construct()
    {
        $this->exerciseService = ExerciseService::getInstance();
        $this->splitService = SplitService::getInstance();
        $this->colorService = ColorService::getInstance();

        parent::__construct();
    }

    public function index()
    {
        Redirect::to('/home');
    }

    public function initalWorkoutData()
    {
        $response = [];
        $response['splitData'] = $this->splitService->splitsOf($this->loggedUser, 1);
        $response['exercises'] = $this->exerciseService->getAllExerciseNames();
        echo json_encode($response);
    }

    public function completeExerciseData()
    {
        echo json_encode($this->exerciseService->getAllExerciseData());
    }

    public function messages($target = false)
    {
        $messagesJSON = [];
        if ($target) {
            $receiverId = $this->userService->getUser($target)->user_id;

            if ($receiverId) {
                $chatId = $this->chatService->startChat($this->loggedUser, $receiverId);
                $messagesJSON = $this->chatService->getMessagesJSONFormat($chatId);
                $this->chatService->setAllMessagesSeen($chatId, $receiverId);
            }
        }

        echo json_encode([
            'ownPicture' => $this->userService->getPicturePathOf($this->loggedUsername),
            'targetPicture' => $this->userService->getPicturePathOf($target),
            'messages' => $messagesJSON
        ]);
    }

    public function rateWorkout($username = null) {
        if (Input::exists() && Token::check(Input::get('token'), 'session/rating_token')) {
            if (Input::keyExists('rating')) {
                $status = ['token' => Token::generate('session/rating_token'), 'result' => ''];

                $user = $this->userService->getUser($username);
                if (!$user) {
                    $status['result'] = 'Error';
                    echo json_encode($status);
                    return;
                }

                $action = $this->splitService->rate($this->loggedUser, $username, Input::get('rating'));

                if (is_string($action)) {
                    $status['result'] = $action;
                    echo json_encode($status);
                } else {
                    echo json_encode($status);
                }
            }
        }
    }

    public function updateWorkoutDay($day)
    {
        if (Input::exists()) {
            if (Token::check(Input::get('token'), 'session/weekday_tokens/' . $day)) {
                $userToBeUpdated = $this->loggedUser;
                if ($this->userService->getLoggedUser()->role_name == Constants::USER_ROLE_ADMIN && Input::keyExists('user')) {
                    $userToBeUpdated = Input::get('user');
                }

                $status = $this->splitService->updateSplit($day, Input::get('data'), $userToBeUpdated);
                $status['token'] = Token::generate('session/weekday_tokens/' . $day);

                echo json_encode($status);
            }
        }
    }

    public function followUser()
    {
        if (Input::exists() && Token::check(Input::get('token'), 'session/follow_token')) {
            $result = ['token' => Token::generate('session/follow_token')];
            if (Input::get('action') == 'Последвай') {
                $this->userService->follow($this->loggedUser, Input::get('followed'));
                $result['status'] = 'success';
            }
            else if (Input::get('action') == 'Отпоследвай') {
                $this->userService->unfollow($this->loggedUser, Input::get('followed'));
                $result['status'] = 'success';
            }
            echo json_encode($result);
        }
    }

    public function updateUserColor() {
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
    }

    public function allColorsHex()
    {
        echo json_encode($this->colorService->getColorData());
    }
}
