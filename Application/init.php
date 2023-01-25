<?php

require __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$GLOBALS['config'] = array(
    'mysql' => array(
        'host' => $_SERVER['DB_HOST'],
        'username' => $_SERVER['DB_USERNAME'],
        'password' => $_SERVER['DB_PASSWORD'],
        'db' => $_SERVER['DB_NAME']
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
        'profile_delete_token' => 'token4',
        'info_update_token' => 'token5',
        'info_create_token' => 'token6',
        'info_delete_token' => 'token7',
    )
);

spl_autoload_register(function ($class) {
    require_once __DIR__ . '/../' . $class . '.php';
});

session_start();