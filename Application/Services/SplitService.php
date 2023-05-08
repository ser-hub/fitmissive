<?php

namespace Application\Services;

use Application\Repositories\{SplitRepository, UserRepository};

class SplitService
{
    private static $instance;
    private $splitRepository;
    private $userRepository;

    private function __construct()
    {
        $this->splitRepository = SplitRepository::getInstance();
        $this->userRepository = UserRepository::getInstance();
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

    public function getRandomisedFollowedSplitsOf($follows = [])
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

    public function updateRating($user_id, $target_id)
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

    public function rate($user_id, $rated, $rating)
    {
        return $this->splitRepository->insertRating(
            $user_id,
            $this->userRepository->find($rated)->user_id,
            $rating
        );
    }

    public function addSplit($day, $username, $title, $sentences)
    {
        return $this->splitRepository->insertSplit(
            $this->userRepository->find($username)->user_id,
            $day, [
                'title' => $title,
                'description' => $sentences,
                'last_updated' => date('Y-m-d H:i:s')
            ]
        );
    }

    public function updateSplit($day, $id, $title, $sentences)
    {
        return $this->splitRepository->updateSplit(
            $day,
            $this->splitRepository->getSplitId($id, $day), [
                'title' => $title,
                'description' => $sentences,
                'last_updated' => date('Y-m-d H:i:s')
            ]
        );
    }
}
