<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Services\SplitService;
use Application\Services\ExerciseService;
use Application\Utilities\{Constants, Redirect, Input, Token, Time};

class Home extends Controller
{
    private $splitService;
    private $exerciseService;
    private $data;

    public function __construct()
    {
        $this->splitService = SplitService::getInstance();
        $this->exerciseService = ExerciseService::getInstance();

        parent::__construct();
    }

    public function index()
    {
        $followedSplits = $this->splitService->getRandomisedFollowedSplitsOf(
            $this->userService->getFollowsArrayOf($this->loggedUser)
        );

        if (!empty($followedSplits)) {
            foreach ($followedSplits as &$split) {
                $split['ratings'] = $this->splitService->getRatingsCountOf($split['user_id']);
                $split['rating'] = $this->splitService->getRating($this->loggedUser, $split['user_id']);
                $split['userPicture'] = $this->userService->getPicturePathOf($split['username']);
                $split['color'] = $this->userService->getUserColor($split['user_id']);
                unset($split['user_id']);

                foreach ($split as $day) {
                    if (isset($day->last_updated)) {
                        $day->last_updated = Time::elapsedString(date_create($day->last_updated));
                    }
                }
            }
        }

        $this->view('home/home', [
            'color' => $this->userService->getUserColor($this->loggedUser),
            'splits' => $this->splitService->splitsOf($this->loggedUser),
            'followedSplits' => $followedSplits,
            'data' => $this->data
        ]);
    }

    public function update($day)
    {
        if (Input::exists()) {
            if (Token::check(Input::get('token'), 'session/weekday_tokens/' . $day)) {
                $response = ['error' => '', 'token' => Token::generate('session/weekday_tokens/' . $day)];
                $inputData = Input::get('data');
                $sentences = preg_split("/\r\n|\r|\n/", $inputData);
                $categories = [];
                //match regex
                $errorFlag = false;
                if ($sentences) {
                    foreach ($sentences as $key => $sentence) {
                        if (preg_match('/\d+ x \d+ (повторения)|(минути)|(секунди) [а-яА-Я\- ]+( #)?/', $sentence)) {
                            $sentenceElements = explode(' ', $sentence);
                            $sets = $sentenceElements[0];
                            $reps = $sentenceElements[2];
                            $comment = '';

                            $hashtagPos = strpos($sentence, '#');

                            if ($hashtagPos) {
                                $comment = trim(substr($sentence, $hashtagPos + 1));
                            } else {
                                $hashtagPos = strlen($sentence);
                            }

                            $exerciseFirstWordIndex = strpos($sentence, $sentenceElements[4]);

                            $exercise = trim(substr($sentence, $exerciseFirstWordIndex, $hashtagPos - $exerciseFirstWordIndex));
                            if (!$this->exerciseService->exerciseExists($exercise)) {
                                $errorFlag = true;
                            }

                            if (!in_array($this->exerciseService->getCategoryOf($exercise), $categories)) {
                                $categories[] = $this->exerciseService->getCategoryOf($exercise);
                            }

                            if ($sets < 1 || $sets > 20 || $reps < 1 || $reps > 100 || strlen($comment) > 80) {
                                $errorFlag = true;
                            }
                        } 
                        
                        if ($errorFlag) {
                            strlen($response['error']) == 0 ? $response['error'] = 'Някои от данните не бяха записани' : 0;
                            unset($sentences[$key]);
                            $errorFlag = false;
                        }
                    }
                }

                //generate title
                $title = 'Почивен';

                if (count($categories) == 1) {
                    $title = $categories[0];
                } else if (count($categories) == 2) {
                    $title = $categories[0] . ' & ' . $categories[1];
                } else if (count($categories) > 2) {
                    $title = 'Комплексна';
                }

                $userToBeUpdated = $this->loggedUser;
                if ($this->userService->getLoggedUser()->role_name == Constants::USER_ROLE_ADMIN && Input::keyExists('user')) {
                    $userToBeUpdated = Input::get('user');
                }

                if ($this->splitService->splitsOf($this->loggedUser)[$day]->description != null) {
                    $this->splitService->updateSplit($day, $userToBeUpdated, $title, implode('\r\n', $sentences));
                } else {
                    $this->splitService->addSplit($day, $userToBeUpdated, $title, implode('\r\n', $sentences));
                }

                if (strlen($response['error']) != 0) {
                    $response['savedSentences'] = $sentences;
                }

                $response['title'] = $title;
                echo json_encode($response);
                return;
            }
        }
        $this->index();
    }

    public function getInitalWorkoutData()
    {
        $response = [];
        $response['splitData'] = $this->splitService->splitsOf($this->loggedUser, 1);
        $response['exercises'] = $this->exerciseService->getAllExerciseData();
        echo json_encode($response);
    }

    public function logout()
    {
        $this->userService->logout();
        Redirect::to('/index');
    }
}
