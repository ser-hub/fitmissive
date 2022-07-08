<?php

namespace Application\Utilities;

class Redirect
{
    public static function to($path)
    {
        header('Location: ' . $path);
        exit();
    }
}
