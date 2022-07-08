<?php

namespace Application\Services;

use Application\Repositories\SplitRepository;
use Application\Utilities\Config;

class SplitService
{
    private static $instance;
    private $splitRepository;

    private function __construct()
    {;
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
