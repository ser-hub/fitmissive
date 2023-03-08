<?php
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
    <?php if ($view === 'profile' || $view === 'search' || $view === 'info') { ?>
        <link rel="stylesheet" href="/css/home.css" type="text/css">
    <?php } ?>

    <?php
    if (str_contains($view, '/')) {
        $view = explode('/', $view);
        $view = end($view);
    }

    ?>

    <link href="/node/node_modules/@fortawesome/fontawesome-free/css/fontawesome.css" rel="stylesheet">
    <link href="/node/node_modules/@fortawesome/fontawesome-free/css/regular.css" rel="stylesheet">
    <link href="/node/node_modules/@fortawesome/fontawesome-free/css/solid.css" rel="stylesheet">

    <link href="/css/common.css" rel="stylesheet" type="text/css">
    <link href="/css/<?= $view ?>.css" rel="stylesheet" type="text/css">
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
</head>

<body>
    <div class="main-content">
        <header class="header-container">
            <a href="/home" id="home"></a>
            <div class="hint"><i>Share your workouts</i></div>
            <?php if ($isLoggedIn) { ?>
                <div class="menu">
                    <form action="/search" class="search-form">
                        <input type="text" class="search-field" name="search" placeholder="Search users" value="<?= isset($data['keyword']) ? $data['keyword'] : "" ?>">
                        <input type="submit" id="submit" value="Search">
                    </form>
                </div>
            <?php } ?>
        </header>