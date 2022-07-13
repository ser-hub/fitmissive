<?php

use Application\Utilities\{Token, Session, Functions};
?>

<!doctype html>
<html>

<head>
    <title>Fitmissive</title>
    <link rel="stylesheet" href="/css/common.css" type="text/css">
    <link rel="stylesheet" href="/css/index.css" type="text/css">
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
</head>

<body>
    <?php require_once 'Application/Views/Common/header.php'; ?>
    <div class="hint"><i>Track your workouts</i></div>
    <form method="POST" action="/index/registerAction">
        <div class="regForm">
            <div>
                <div class="table">
                    <div class="row">
                        <div style="display:table-cell; text-align:center; padding:10px 30px;">
                            <legend>Register here if you don't have an account.</legend>
                        </div>
                    </div>
                </div>
                <div class="table">
                    <div class="row">
                        <div class="cell">
                            <label for="usernameReg">Username:</label>
                        </div>
                        <div class="cell">
                            <input type="text" name="usernameReg" id="usernameReg" value="<?php if (isset($data['RegInput']['usernameReg'])) echo Functions::escape($data['RegInput']['usernameReg']) ?>" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="cell">
                            <label for="email">Email:</label>
                        </div>
                        <div class="cell">
                            <input type="text" name="email" id="email" value="<?php if (isset($data['RegInput']['email'])) echo Functions::escape($data['RegInput']['email']) ?>" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="cell">
                            <label for="passwordReg">Password:</label>
                        </div>
                        <div class="cell">
                            <input type="password" name="passwordReg" id="passwordReg" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="cell">
                            <label for="password2">Repeat password:</label>
                        </div>
                        <div class="cell">
                            <input type="password" name="password2" id="password2" required />
                        </div>
                    </div>
                </div>
                <div>
                    <div>
                        <div class="row-custom">
                            <input type="hidden" name="RegisterToken" value="<?php echo Token::generate('session/register_token'); ?>">
                            <input type="submit" name="submitRegistration" value="Register">
                        </div>
                    </div>
                    <!-- Errors -->
                    <div class="row-custom">
                        <?php
                        $errorsLabel = 'RegErrors';
                        if (isset($data[$errorsLabel]) || Session::exists('error') || Session::exists('success')) {
                            require_once 'Application/Views/Common/error-section.php';
                        }
                        ?>
                    </div>
                    <!-- End of Errors -->
                </div>
            </div>
        </div>
    </form>
    <form method="POST" action="/index/loginAction">
        <div class="loginForm">
            <div>
                <div class="table">
                    <div class="row">
                        <div style="display:table-cell; text-align:center; padding:10px 30px;">
                            <legend>Already have and account? Log in!</legend>
                        </div>
                    </div>
                </div>
                <div class="table">
                    <div class="row">
                        <div class="cell">
                            <label for="username">Username:</label>
                        </div>
                        <div class="cell">
                            <input type="text" name="username" id="username" value="<?php if (isset($data['LogInput']['username'])) echo Functions::escape($data['LogInput']['username']) ?>" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="cell">
                            <label for="password">Password:</label>
                        </div>
                        <div class="cell">
                            <input type="password" name="password" id="password" value="<?php if (isset($data['LogInput']['password'])) echo Functions::escape($data['LogInput']['password']) ?>" required />
                        </div>
                    </div>
                </div>
                <div>
                    <div>
                        <div class="row-custom">
                            <input type="hidden" name="LoginToken" value="<?php echo Token::generate('session/login_token'); ?>">
                            <input type="submit" name="submitLogin" value="Login">
                        </div>
                    </div>
                    <div>
                        <div class="row-custom">
                            Forgot your password? <a href="/index/forgotPassword">Click here to reset it.</a>
                        </div>
                    </div>
                    <!-- Errors -->
                    <div class="row">
                        <?php
                        $errorsLabel = 'LogErrors';
                        if (isset($data[$errorsLabel])) {
                            require_once 'Application/Views/Common/error-section.php';
                        }
                        ?>
                    </div>
                    <!-- End of Errors -->
                </div>
            </div>
        </div>
    </form>
    <?php require_once 'Application/Views/Common/footer.php'; ?>
</body>

</html>