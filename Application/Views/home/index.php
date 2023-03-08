<?php

use Application\Utilities\{Token, Session, Functions};

?>

<?php require_once 'Application/Views/Common/header.php' ?>

<div class="index-content">
    <div class="forms">
        <div class="reg-form">
            <form method="POST" action="/index/registerAction">
                <div class="row">
                    <legend>Register here if you don't have an account.</legend>
                </div>
                <div class="row">
                    <label for="usernameReg">Username:</label>
                    <input type="text" name="usernameReg" id="usernameReg" value="<?php if (isset($data['RegInput']['usernameReg'])) echo Functions::escape($data['RegInput']['usernameReg']) ?>" required />
                </div>
                <div class="row">
                    <label for="email">Email:</label>
                    <input type="text" name="email" id="email" value="<?php if (isset($data['RegInput']['email'])) echo Functions::escape($data['RegInput']['email']) ?>" required />
                </div>
                <div class="row">
                    <label for="passwordReg">Password:</label>
                    <input type="password" name="passwordReg" id="passwordReg" required />
                </div>
                <div class="row">
                    <label for="password2">Repeat password:</label>
                    <input type="password" name="password2" id="password2" required />
                </div>
                <div class="row row-btn">
                    <input type="hidden" name="RegisterToken" value="<?php echo Token::generate('session/register_token'); ?>">
                    <input type="submit" name="submitRegistration" value="Register">
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

        <div class="login-form">
            <form method="POST" action="/index/loginAction">
                <div class="row">
                    <legend>Already have and account? Log in!</legend>
                </div>
                <div class="row">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" value="<?php if (isset($data['LogInput']['username'])) echo Functions::escape($data['LogInput']['username']) ?>" required />
                </div>
                <div class="row">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" value="<?php if (isset($data['LogInput']['password'])) echo Functions::escape($data['LogInput']['password']) ?>" required />
                </div>
                <div class="row row-btn">
                    <input type="hidden" name="LoginToken" value="<?php echo Token::generate('session/login_token'); ?>">
                    <input type="submit" name="submitLogin" value="Login">
                </div>
                <div class="row row-btn">
                    Forgot your password? <a href="/index/forgotPassword">Click here to reset it.</a>
                </div>
                <?php
                $errorsLabel = 'LogErrors';
                if (isset($data[$errorsLabel])) {
                    echo '<div class="row row-errors">';
                    require_once 'Application/Views/Common/error-section.php';
                    echo '</div>';
                }
                ?>
            </form>
        </div>
    </div>
</div>
<?php require_once 'Application/Views/Common/footer.php' ?>