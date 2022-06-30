<?php

session_start();

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
        'session_name' => 'user'
    )
);

spl_autoload_register(function($class) {
    require_once 'application/utilities/' . $class . '.php';
});

require_once 'application/utilities/functions/sanitize.php';
require_once 'application/core/app.php';
require_once 'application/core/controller.php';
require_once 'application/database/database.php';