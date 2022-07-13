<?php

namespace Application\utilities;

class Functions
{
    public static function escape($string)
    {
        return htmlentities($string, ENT_QUOTES, 'UTF-8');
    }

    public static function getProfilePicPath($userId)
    {
        return file_exists('/img/profile/' . $userId . '.jpg') ? '/img/profile/'  . $userId . '.jpg' : '/img/profile/default.png';
    }
}
