<?php

namespace Application\Services;

use Application\Repositories\InfoRepository;

class InfoService
{
    private static $instance;
    private $infoRepository;
    
    private function __construct()
    {
        $this->infoRepository = InfoRepository::getInstance();
    }
    
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    public function addInfo($info)
    {
        return $this->infoRepository->add($info);
    }

    public function getAllInfo()
    {
        return $this->infoRepository->getAll();
    }

    public function getInfoBySlug($slug)
    {
        return $this->infoRepository->find($slug);
    }
    
    public function updateInfo($title, $fields)
    {
        return $this->infoRepository->update($title, $fields); 
    }

    public function deleteInfo($slug)
    {
        return $this->infoRepository->delete($slug);
    }
}