<?php

use Application\Utilities\Input;

$isLoggedIn = isset($data['loggedUser']);
if ($isLoggedIn) {
    $menu = $data['menu'];
}
$unseenMessages = isset($data['unseenMessages']) ? $data['unseenMessages'] : false;
?>

<!doctype html>
<html>

<head>
    <title>Fitmissive</title>
    <meta charset="utf-8">
    <?php if ($view === 'profile' || $view === 'home/home') { ?>
        <meta name="viewport" class="carousel-content">
        <link rel="stylesheet" href="/node/assets/css/bootstrap.css">
    <?php } ?>
    <?php if ($view === 'profile' || $view === 'search') { ?>
        <link rel="stylesheet" href="/css/home.css" type="text/css">
    <?php } ?>

    <?php

    if (str_contains($view, '/')) {
        $view = explode('/', $view);
        $view = end($view);
    }

    ?>

    <link rel="stylesheet" href="/css/common.css" type="text/css">
    <link rel="stylesheet" href="/css/<?= $view ?>.css" type="text/css">
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
</head>

<body>

    <header class="header-container" style="<?php if (!$isLoggedIn) echo 'justify-content: center' ?>">
        <a href="/home" id="home"></a>
        <?php if ($isLoggedIn) { ?>
            <div class="menu">
                <div class="items">
                    <form action="/search" style="margin-right:30px">
                        <input type="text" class="search-field" name="search" placeholder="Search users" style="margin: 8px" value="<?php if (isset($data['keyword'])) echo $data['keyword'] ?>">
                        <input type="submit" class="menu-item" style="<?php if (str_contains(Input::get('url'), 'search')) echo 'background-color: #ADE8F4' ?>" value="Search">
                    </form>
                    <?php foreach ($menu as $key => $value) { ?>
                        <a href="<?= $value ?>" class="menu-item <?php
                                                                    if (str_contains('/' . Input::get('url'), $value)) echo 'menu-selected ';
                                                                    if ($value == '/messenger' && $unseenMessages) echo 'notification'
                                                                    ?>" style="margin-right: 10px"><?= $key ?></a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </header>

    <script src="/node/node_modules/socket.io/client-dist/socket.io.js"></script>
    <script src="/Application/Socket/utils.js"></script>
    <?php include "Application/Socket/connect.php" ?>