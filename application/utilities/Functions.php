<?php

namespace Application\utilities;

class Functions
{
    public static function escape($string)
    {
        return htmlentities($string, ENT_QUOTES, 'UTF-8');
    }
}
