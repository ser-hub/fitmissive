<?php

use Application\Utilities\Input;

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
</div>
<?php if (isset($data['loggedUser'])) { ?>
    <div class="side-bar">
        <a href="/home" class="home-btn <?= $stylesheet == 'home' ? " menu-selected" : "" ?>">
            <i class="fa-<?= $stylesheet == 'home' ? "solid" : "regular" ?> fa-newspaper fa-xl"></i>
            Начало
        </a>

        <a href="/profile" class="<?= strcmp(Input::get('url'), 'profile') == 0 || strcmp(Input::get('url'), 'profile/' . $data['loggedUsername']) == 0 ? " menu-selected" : "" ?>">
            <i class="fa-<?= strcmp(Input::get('url'), 'profile') == 0 || strcmp(Input::get('url'), 'profile/' . $data['loggedUsername']) == 0 ? "solid" : "regular" ?> fa-user fa-xl"></i>
            Моят профил
        </a>

        <a href="/messenger" class="<?= $stylesheet == 'messenger' ? " menu-selected" : "" ?>">
            <i class="fa-<?= $stylesheet == 'messenger' ? "solid" : "regular" ?> fa-message fa-xl"></i>
            Съобщения
            <div class="notification"><?= $data['unseenMessages'] > 0 ? $data['unseenMessages'] : '' ?></div>
        </a>

        <?php if ($data['isAdmin']) { ?>
        <a href="/admin" class="<?= $stylesheet == 'admin' ? " menu-selected" : "" ?>">
            <i class="fa-<?= $stylesheet == 'admin' ? "solid" : "regular" ?> fa-file-code fa-xl"></i>
            Администраторски панел
        </a>
        <?php } ?>

        <a href="/help" class="<?= $stylesheet == 'help' ? " menu-selected" : "" ?>" style="margin-top: auto">
            <i class="fa-solid fa-magnifying-glass fa-xl"></i>
            Упражнения
        </a>

        <a href="/home/logout" class="logout-btn">
            <i class="fa-solid fa-door-open fa-xl"></i>
            Изход
        </a>
    </div>
<?php } ?>
</body>

</html>