<?php

namespace Application\Services;

use Application\Repositories\SplitRepository;
use Application\Utilities\Config;

class SplitService
{
    private static $instance;
    private $splitRepository;

    private function __construct()
    {
        $this->splitRepository = SplitRepository::getInstance();

        $this->_sessionName = Config::get('session/session_name');
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function splitsOf($user_id)
    {
        return $this->splitRepository->getUserSplits($user_id);
    }

    public function getRandomisedFollowedSplitsOf($follows = [])
    {
        $allSplits = [];

        if ($follows) {
            foreach ($follows as $follow) {
                $split = $this->splitsOf($follow)[date('l')];

                if ($split != null) {
                    $allSplits[] = $split;
                }
            }

            if (count($allSplits) > 15) {
                $randKeys = array_rand($allSplits, rand(8, 15));
                $randomSplits = [];
                foreach ($randKeys as $key) {
                    $randomSplits[] = $allSplits[$key];
                }

                $allSplits = $randomSplits;
            }
            shuffle($allSplits);
        }
        return $allSplits;
    }

    public function addSplit($user_id, $day, $data = [])
    {
        return $this->splitRepository->insertSplit($user_id, $day, $data);
    }

    public function updateSplit($day, $id, $data = [])
    {
        return $this->splitRepository->updateSplit(
            $day,
            $this->splitRepository->getSplitId($id, $day),
            $data
        );
    }
}
