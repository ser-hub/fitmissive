<?php

use Application\Utilities\{Token, Session, Functions, Input};
?>

<div class="index-content">
    <div class="letter">
        <div class="letter-header">
            <img src="/img/logo-white.png" width="50px" height="50px" style="margin-right:5px">
            Fitmissive<br>
            Сподели своята тренировка
        </div>
        <div class="pages">
            <div class="page">
                <form method="POST" action="/index/registerAction">
                    <div class="row">
                        <legend>Нямаш акаунт? Регистрирай се тук.</legend>
                    </div>
                    <div class="row">
                        <label for="username-reg">Потребителско име:</label>
                        <input type="text" name="username-reg" id="username-reg" value="<?= Input::keyExists('usernameReg') ? Functions::escape(Input::get('usernameReg')) : '' ?>" required />
                    </div>
                    <div class="row">
                        <label for="email">Имейл:</label>
                        <input type="text" name="email" id="email" value="<?= Input::keyExists('email') ? Functions::escape(Input::get('email')) : '' ?>" required />
                    </div>
                    <div class="row">
                        <label for="password-reg">Парола:</label>
                        <input type="password" name="password-reg" id="password-reg" required />
                    </div>
                    <div class="row">
                        <label for="password2">Повторете паролата:</label>
                        <input type="password" name="password2" id="password2" required />
                    </div>
                    <div class="row row-btn">
                        <input type="hidden" name="register-token" value="<?= Token::generate('session/register_token'); ?>">
                        <input type="submit" value="Регистрирай се">
                    </div>
                    <?php
                    $errorsLabel = 'regErrors';
                    if (isset($data[$errorsLabel]) || Session::exists('success')) {
                        echo '<div class="row row-errors">';
                        require_once 'Application/Views/Common/error-section.php';
                        echo '</div>';
                    }
                    ?>
                </form>
            </div>

            <div class="page">
                <form method="POST" action="/index/loginAction">
                    <div class="row">
                        <legend>Влез в акаунта си.</legend>
                    </div>
                    <div class="row">
                        <label for="username">Потребителско име:</label>
                        <input type="text" name="username" id="username" value="<?= Input::keyExists('username') ? Functions::escape(Input::get('username')) : '' ?>" />
                    </div>
                    <div class="row">
                        <label for="password">Парола:</label>
                        <input type="password" name="password" id="password"/>
                    </div>
                    <div class="row row-btn">
                        <input type="hidden" name="login-token" value="<?= Token::generate('session/login_token'); ?>">
                        <input type="submit" value="Влизане">
                    </div>
                    <div class="row row-btn">
                        <a href="#" id="forgotten-password">Забравих паролата си</a>
                    </div>
                    <?php
                    $errorsLabel = 'logErrors';
                    if (isset($data[$errorsLabel])) {
                        echo '<div class="row row-errors">';
                        require_once 'Application/Views/Common/error-section.php'; // do something about error-section.php
                        echo '</div>';
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>
</div>