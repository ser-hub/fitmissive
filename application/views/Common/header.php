<?php

use Application\Utilities\Input;

$isLoggedIn = isset($data['loggedUser']);
if ($isLoggedIn) {
    $menu = $data['menu'];
}
$unseenMessages = isset($data['unseenMessages']) ? $data['unseenMessages'] : false;
?>

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