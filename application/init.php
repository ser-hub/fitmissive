<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$GLOBALS['config'] = array(
    'mysql' => array(
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'db' => 'fitmissive'
    ),
    'remember' => array(
        'cookie_name' => 'hash',
        'cookie_expiry' => 604800
    ),
    'session' => array(
        'session_name' => 'user',
        'register_token' => 'token1',
        'login_token' => 'token2',
        'weekday_tokens' => array (
            'Monday' => 'monday_token',
            'Tuesday' => 'tuesday_token',
            'Wednesday' => 'wednesday_token',
            'Thursday' => 'thursday_token',
            'Friday' => 'friday_token',
            'Saturday' => 'saturday_token',
            'Sunday' => 'sunday_token'
        ),
        'profile_edit_token' => 'token3',
    )
);

spl_autoload_register(function ($class) {
    require_once __DIR__ . '/../' . $class . '.php';
});

session_start();
