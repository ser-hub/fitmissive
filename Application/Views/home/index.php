<?php
    use Application\Utilities\{Token, Session, Functions};
?>

<div class="index-content">
    <div class="letter">
        <div class="page">
            <form method="POST" action="/index/registerAction">
                <div class="row">
                    <legend>Нямаш акаунт? Регистрирай се тук.</legend>
                </div>
                <div class="row">
                    <label for="usernameReg">Потребителско име:</label>
                    <input type="text" name="usernameReg" id="usernameReg" value="<?= isset($data['RegInput']['usernameReg']) ? Functions::escape($data['RegInput']['usernameReg']) : '' ?>" required />
                </div>
                <div class="row">
                    <label for="email">Имейл:</label>
                    <input type="text" name="email" id="email" value="<?= isset($data['RegInput']['email']) ? Functions::escape($data['RegInput']['email']) : '' ?>" required />
                </div>
                <div class="row">
                    <label for="passwordReg">Парола:</label>
                    <input type="password" name="passwordReg" id="passwordReg" required />
                </div>
                <div class="row">
                    <label for="password2">Повторете паролата:</label>
                    <input type="password" name="password2" id="password2" required />
                </div>
                <div class="row row-btn">
                    <input type="hidden" name="RegisterToken" value="<?= Token::generate('session/register_token'); ?>">
                    <input type="submit" value="Регистрирай се">
                </div>
                <?php
                $errorsLabel = 'RegErrors';
                if (isset($data[$errorsLabel]) || Session::exists('error') || Session::exists('success')) {
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
                    <input type="text" name="username" id="username" value="<?= isset($data['LogInput']['username']) ? Functions::escape($data['LogInput']['username']) : '' ?>" />
                </div>
                <div class="row">
                    <label for="password">Парола:</label>
                    <input type="password" name="password" id="password" value="<?= isset($data['LogInput']['password']) ? Functions::escape($data['LogInput']['password']) : '' ?>" />
                </div>
                <div class="row row-btn">
                    <input type="hidden" name="LoginToken" value="<?= Token::generate('session/login_token'); ?>">
                    <input type="submit" value="Влизане">
                </div>
                <div class="row row-btn"> 
                    <a href="#" id="forgotten-password">Забравих паролата си</a>
                </div>
                <?php
                $errorsLabel = 'LogErrors';
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