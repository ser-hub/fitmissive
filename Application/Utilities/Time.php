<?php

namespace Application\Utilities;

use DateTime;

class Time
{
    public static function elapsedString(DateTime $timestampPast)
    {
        $interval = date_create()->diff($timestampPast);
        if ($interval->y > 0) {
            if ($interval->y > 1) return 'преди ' . $interval->y . ' години';
                             else return 'преди ' . $interval->y . ' година';
            
        } elseif ($interval->m > 0) {
            if ($interval->m > 1) return 'преди ' . $interval->m . ' месеца';
                             else return 'преди ' . $interval->m . ' месец';
            
        } elseif ($interval->d > 0) {
            if ($interval->d > 1) return 'преди ' . $interval->d . ' дни';
                             else return 'преди ' . $interval->d . ' ден';
            
        } elseif ($interval->h > 0) {
            if ($interval->h > 1) return 'преди ' . $interval->h . ' часа';
                             else return 'преди ' . $interval->h . ' час';
        } elseif ($interval->i > 0) {
            if ($interval->i > 1) return 'преди ' . $interval->i . ' минути';
                             else return 'преди ' . $interval->i . ' минута';
        }
    }

    public static function elapsedMinutes(DateTime $timestampPast) {
        $interval = date_create()->diff($timestampPast);
        return $interval->y * 525600 + $interval->m * 43800 + $interval->h * 1440 + $interval->i; 
    }
}