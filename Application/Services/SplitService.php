<?php

namespace Application\Services;

use Application\Repositories\{SplitRepository, UserRepository};
use Application\Utilities\Time;

class SplitService
{
    private static $instance;
    private $splitRepository,
            $userRepository,
            $userService;
    private $exerciseService;

    private function __construct()
    {
        $this->splitRepository = SplitRepository::getInstance();
        $this->userRepository = UserRepository::getInstance();
        $this->exerciseService = ExerciseService::getInstance();
        $this->userService = UserService::getInstance();
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function splitsOf($user_id, $api_mode = null)
    {
        if ($api_mode) {
            $raw_splits = $this->splitRepository->getUserSplits($user_id);
            $splitsData = [];
            foreach ($raw_splits as $key => $value) {
                if ($value) {
                    $splitsData[$key] = $value->description;
                } else {
                    $splitsData[$key] = '';
                }
            }
            return $splitsData;
        } else {
            return $this->splitRepository->getUserSplits($user_id);
        }
    }

    private function getRandomisedFollowedSplitsOf($follows = [])
    {
        $allSplits = [];

        if ($follows) {
            shuffle($follows);
            foreach ($follows as $follow) {
                $split = $this->splitsOf($follow);
                $user = $this->userRepository->find($follow);
                $user_id = $user->fullname != null ? $user->fullname : $user->username;
                $split['username'] = $user->username;
                $split['user_id'] = $user->user_id;
                if ($split != null) {
                    $allSplits[$user_id] = $split;
                }
            }

            if (count($allSplits) > 15) {
                $randKeys = array_rand($allSplits, rand(8, 15));
                $randomSplits = [];
                foreach ($randKeys as $key) {
                    $randomSplits[$key] = $allSplits[$key];
                }

                $allSplits = $randomSplits;
            }
        }
        return $allSplits;
    }

    public function getFollowedSplits($loggedUser, $follows = [])
    {
        $data = $this->getRandomisedFollowedSplitsOf($follows);

        if (!empty($data)) {
            foreach ($data as &$split) {
                $split['ratings'] = $this->getRatingsCountOf($split['user_id']);
                $split['rating'] = $this->getRating($loggedUser, $split['user_id']);
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

        return $data;
    }

    public function getRatingsCountOf($target_id)
    {
        $data = $this->splitRepository->getAllRatingsOf($target_id);

        $likesCount = 0;
        $dislikesCount = 0;

        if ($data) {
            foreach ($data as $rating) {
                if ($rating->rating) $likesCount++;
                else $dislikesCount++;
            }
        }
        return ['likes' => $likesCount, 'dislikes' => $dislikesCount];
    }

    public function rate($user_id, $target, $rating)
    {
        $target = $this->userRepository->find($target)->user_id;
        if ($this->getRating($user_id, $target) === null) {
            if ($this->addRating($user_id, $target, $rating)) {
                return 'Rated';
            } else {
                return 'Error';
            }
        } else if ($this->getRating($user_id, $target) != $rating) {
            if (!$this->updateRating($user_id, $target, $rating)) {
                return 'Updated';
            } else {
                return 'Error';
            }
        } else if ($this->getRating($user_id, $target) === $rating) {
            return;
        } else {
            return 'Error';
        }
    }

    private function addRating($user_id, $target, $rating)
    {
        return $this->splitRepository->insertRating(
            $user_id,
            $target,
            $rating
        );
    }

    public function getRating($user_id, $target_id)
    {
        if ($user_id == $target_id) return null;

        $data = $this->splitRepository->getAllRatingsOf($target_id);
        if ($data) {
            foreach ($data as $rating) {
                if ($rating->user_id == $user_id) {
                    return $rating->rating;
                }
            }
        }
        return null;
    }

    private function updateRating($user_id, $target_id)
    {
        $data = $this->splitRepository->getAllRatingsOf($target_id);

        if ($data) {
            foreach ($data as $rating) {
                if ($rating->user_id == $user_id) {
                    if ($rating->rating == 0)
                        return $this->splitRepository->updateRating($rating->rating_id, 1);
                    else
                        return $this->splitRepository->updateRating($rating->rating_id, 0);
                }
            }
        }
    }

    public function addSplit($day, $username, $title, $sentences)
    {
        return $this->splitRepository->insertSplit(
            $this->userRepository->find($username)->user_id,
            $day,
            [
                'title' => $title,
                'description' => $sentences,
                'last_updated' => date('Y-m-d H:i:s')
            ]
        );
    }

    public function updateSplit($day, $data, $userId)
    {
        $result = ['error' => ''];
        $sentences = preg_split("/\r\n|\r|\n/", $data);
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
                    if (strlen($result['error']) == 0) {
                        $result['error'] = 'Някои от данните не бяха записани';
                    }
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

        if ($this->splitsOf($userId)[$day] != null) {
            if (!$this->splitRepository->updateSplit(
                $day,
                $this->splitRepository->getSplitId($userId, $day),
                [
                    'title' => $title,
                    'description' => implode('\r\n', $sentences),
                    'last_updated' => date('Y-m-d H:i:s')
                ]
            )) {
                $result['error'] = 'Грешка при записването на данните.';
            };
        } else {
            $this->addSplit($day, $userId, $title, implode('\r\n', $sentences));
        }

        if (strlen($result['error']) != 0) {
            $result['savedSentences'] = preg_split("/\r\n|\r|\n/", $this->splitsOf($userId)[$day]->description);
        }

        $result['title'] = $title;
        return $result;
    }
}
