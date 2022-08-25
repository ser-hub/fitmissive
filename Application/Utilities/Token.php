<?php

namespace Application\Utilities;

class Token
{
    public static function generate($tokenName)
    {
        return Session::put(Config::get($tokenName), hash('sha256', uniqid()));
    }

    public static function check($token, $tokenPath)
    {
        $tokenName = Config::get($tokenPath);
        if (Session::exists($tokenName) && $token === Session::get($tokenName)) {
            Session::delete($tokenName);
            return true;
        }
        return false;
    }
}
