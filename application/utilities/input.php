<?php

namespace Application\Utilities;

//wrapper class for user input
class Input
{
    public static function exists($type = 'post')
    {
        switch ($type) {
            case 'post':
                return (!empty($_POST)) ? true : false;
                break;

            case 'get':
                return (!empty($_GET)) ? true : false;
                break;

            default:
                return false;
                break;
        }
    }

    public static function keyExists($key)
    {
        return array_key_exists($key, $_POST) || array_key_exists($key, $_GET) || array_key_exists($key, $_FILES);
    }

    public static function get($item)
    {
        if (isset($_POST[$item])) {
            return $_POST[$item];
        } elseif (isset($_GET[$item])) {
            return $_GET[$item];
        } elseif (isset($_FILES[$item])) {
            return $_FILES[$item];
        } else {
            return '';
        }
    }
}
