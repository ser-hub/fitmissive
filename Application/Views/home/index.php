<?php
    use Application\Utilities\{Token, Session, Functions};
?>

<?php require_once 'Application/Views/Common/header.php' ?>

<div class="index-content">
    <div class="forms">
        <div class="reg-form">
            <form method="POST" action="/index/registerAction">
                <div class="row">
                    <legend>Нямаш акаунт? Регистрирай се тук.</legend>
                </div>
                <div class="row">
                    <label for="usernameReg">Потребителско име:</label>
                    <input type="text" name="usernameReg" id="usernameReg" value="<?php if (isset($data['RegInput']['usernameReg'])) echo Functions::escape($data['RegInput']['usernameReg']) ?>" required />
                </div>
                <div class="row">
                    <label for="email">Имейл:</label>
                    <input type="text" name="email" id="email" value="<?php if (isset($data['RegInput']['email'])) echo Functions::escape($data['RegInput']['email']) ?>" required />
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
                    <input type="hidden" name="RegisterToken" value="<?php echo Token::generate('session/register_token'); ?>">
                    <input type="submit" name="submitRegistration" value="Регистрирай се">
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
                    <legend>Влез в акаунта си.</legend>
                </div>
                <div class="row">
                    <label for="username">Потребителско име:</label>
                    <input type="text" name="username" id="username" value="<?php if (isset($data['LogInput']['username'])) echo Functions::escape($data['LogInput']['username']) ?>" />
                </div>
                <div class="row">
                    <label for="password">Парола:</label>
                    <input type="password" name="password" id="password" value="<?php if (isset($data['LogInput']['password'])) echo Functions::escape($data['LogInput']['password']) ?>" />
                </div>
                <div class="row row-btn">
                    <input type="hidden" name="LoginToken" value="<?php echo Token::generate('session/login_token'); ?>">
                    <input type="submit" name="submitLogin" value="Влизане">
                </div>
                <div class="row row-btn"> 
                    <a href="#" id="forgotten-password">Забравена парола?</a>
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
<script>
    let forgottenPassBtn = document.getElementById("forgotten-password");
    forgottenPassBtn.onclick = function() {
        forgottenPassBtn.parentElement.innerHTML = `
        <label for="fp-email">Въведете имейл:</label>
        <input type="text" name="fp-email" id="fp-email-input"/>
        <button>Изпрати имейл</button>
        `
    };
</script>
<?php require_once 'Application/Views/Common/footer.php' ?>