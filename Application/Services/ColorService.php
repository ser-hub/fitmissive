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

    public function updateColor($id, $value)
    {
        $lengthValidate = strlen($value) == 6;
        $rangeValidate = true;
        if ($lengthValidate) {
            for ($i = 0; $i < 5; $i += 2) {
                $pair = hexdec($value[$i] . $value[$i + 1]);
                if ($pair < 0 || $pair > 255) {
                    $rangeValidate = false;
                    break;
                }
            }
        }

        if ($rangeValidate && $lengthValidate) {
            return $this->colorRepository->update($id, $value);
        } else {
            return 'Невалидна стойност';
        }
    }

    public function getAllColorsHex()
    {
        return array_map(function ($color) {
            return $color->color_hex;
        }, $this->colorRepository->getAll());
    }

    public function getColorData()
    {
        $data = $this->colorRepository->getAll();
        $result = [];

        foreach ($data as $value) {
            $result[] = [
                "id" => $value->color_id,
                "value" => $value->color_hex
            ];
        }

        return $result;
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
