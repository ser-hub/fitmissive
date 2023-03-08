<?php

use Application\Utilities\Input;

$isLoggedIn = isset($data['loggedUser']);
?>
<footer class="footer-container">
    <div class="infos">
        <div class="footer-items">
            <a href="/home">Home</a>
            <?php
            if ($data['info']) {
                foreach ($data['info'] as $info) {
                    echo ' | <a href="/info/' . $info->slug . '">' . $info->title . '</a>';
                }
            }
            ?>
        </div>
    </div>
</footer>
<script src="/node/node_modules/socket.io/client-dist/socket.io.js"></script>
<script src="/Application/js/utils.js"></script>
<?php include "Application/Socket/connect.php" ?>
</div>
<?php if ($isLoggedIn) { ?>
    <div class="side-bar">
        <a href="/home" class="home-btn <?= str_contains(Input::get('url'), 'home') ? " menu-selected" : "" ?>">
            <i class="fa-<?= str_contains(Input::get('url'), 'home') ? "solid" : "regular" ?> fa-flag fa-xl"></i>
            Home
        </a>

        <a href="/profile" class="<?= strcmp(Input::get('url'), 'profile') == 0 || strcmp(Input::get('url'), 'profile/' . $data['loggedUsername']) == 0 ? " menu-selected" : "" ?>">
            <i class="fa-<?= strcmp(Input::get('url'), 'profile') == 0 || strcmp(Input::get('url'), 'profile/' . $data['loggedUsername']) == 0 ? "solid" : "regular" ?> fa-user fa-xl"></i>
            Profile
        </a>

        <a href="/messenger" class="<?= str_contains(Input::get('url'), 'messenger') ? " menu-selected" : "" ?>">
            <i class="fa-<?= str_contains(Input::get('url'), 'messenger') ? "solid" : "regular" ?> fa-paper-plane fa-xl"></i>
            Messages
        </a>

        <a href="/home/logout" class="logout-btn">
            <i class="fa-solid fa-door-open fa-xl"></i>
            Log out
        </a>
    </div>
<?php } ?>
</body>

</html>