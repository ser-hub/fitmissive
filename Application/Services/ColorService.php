<?php

namespace Application\Services;

use Application\Repositories\ColorRepository;

class ColorService
{
    private static $instance;
    private $colorRepository;

    private function __construct()
    {
        $this->colorRepository = ColorRepository::getInstance();
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getAllColorsHex()
    {
        return array_map(function ($color) {
            return $color->color_hex;
        }, $this->colorRepository->getAll());
    }

    public function getColorId($value)
    {
        $color = $this->colorRepository->getColorByHex($value);
        if ($color) {
            return $color->color_id;
        }
        return false;
    }
}
