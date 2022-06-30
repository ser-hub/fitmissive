<?php
require_once 'application/repositories/userRepository.php';

if (!UserRepository::getInstance()->getAllUsers()->error())
{
    echo 'OK';
}
else
{
    echo 'No users found';
}