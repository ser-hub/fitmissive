<?php

$unseenMessages = isset($data['unseenMessages']) ? $data['unseenMessages'] : false;

?>

<!doctype html>
<html lang="bg">

<head>
    <title>Fitmissive</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php if ($view == 'profile/profile' || $view == 'home/home') { ?>
        <meta name="viewport" class="carousel-content">
        <link rel="stylesheet" href="/node/assets/css/bootstrap.css">
    <?php }
    if ($view == 'profile/profile' || $view == 'search' || $view == 'info') { ?>
        <link rel="stylesheet" href="/css/home.css" type="text/css">
    <?php } ?>

    <?php
    $stylesheet = $view;

    if (str_contains($stylesheet, '/')) {
        $stylesheet = explode('/', $stylesheet);
        $stylesheet = end($stylesheet);
    }

    if ($stylesheet == 'index') {
        if (rand(1, 2) % 2 == 0) {
            $pattern = 'plus';
        } else {
            $pattern = 'circle';
        } ?>
        <link rel="stylesheet" href="/css/patterns/body-pattern-<?= $pattern ?>-animated.css" type="text/css">
    <?php } elseif ($stylesheet == 'admin') { ?>
        <link href="/css/help.css" rel="stylesheet" type="text/css">
    <?php } ?>

    <link href="/node/node_modules/@fortawesome/fontawesome-free/css/fontawesome.css" rel="stylesheet">
    <link href="/node/node_modules/@fortawesome/fontawesome-free/css/regular.css" rel="stylesheet">
    <link href="/node/node_modules/@fortawesome/fontawesome-free/css/solid.css" rel="stylesheet">

    <link href="/css/common.css" rel="stylesheet" type="text/css">
    <link href="/css/colors/colors.css" rel="stylesheet" type="text/css">
    <link href="/css/<?= $stylesheet ?>.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="main-content">
        <header class="header-container">
            <a href="/home" id="home">
                <img src="/img/logo-white.png" width="60px" height="60px" style="margin-right:5px">
            </a>
            <div class="hint"><i>Fitmissive</i></div>
            <?php if (isset($data['loggedUser'])) { ?>
                <div class="menu">
                    <form action="/search" class="search-form">
                        <input type="text" class="search-field" name="search" placeholder="Потърси някого" value="<?= isset($data['keyword']) ? $data['keyword'] : "" ?>">
                        <input type="submit" id="submit" value="Tърси">
                    </form>
                </div>
            <?php } ?>
        </header>