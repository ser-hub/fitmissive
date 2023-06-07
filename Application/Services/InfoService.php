<?php

namespace Application\Services;

use Application\Repositories\InfoRepository;
use Application\Utilities\Validator;

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
        $validator = new Validator();
        $validator->check($info, [
            'title' => [
                'name' => 'Заглавието',
                '!contains' => '\\/?#@*=;\'"',
                'required' => true,
                'max' => 45
            ],
            'slug' => [
                'name' => 'Slug',
                'required' => true,
                'max' => 45,
                '!contains' => ' \\/?%&#@!*()+=,.;:\'"',
                'unique' => 'info',
                'dbColumn' => 'slug'
            ],
            'content' => [
                'name' => 'Съдържанието',
                'required' => true,
                'max' => 4000
            ]
        ]);

        if ($this->getInfoBySlug($info['slug'])) {
            return ['Такава инфо страница вече съществува.'];
        }

        if ($validator->passed()) {
            return $this->infoRepository->add($info);
        } else {
            return $validator->errors();
        }
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

    public function deleteInfo($id)
    {
        return $this->infoRepository->delete($id);
    }
}
