<?php

use Application\Utilities\Token;
?>
<div class="index-content">
    <div class="letter">
        <div class="page" style="border-right: 0">
            <form method="POST" action="/index/updatePassword">
                <div class="row">
                    <legend>Сменете паролата си тук.</legend>
                </div>
                <div class="row">
                    <label for="password">Нова парола:</label>
                    <input type="password" name="password" id="password" required />
                </div>
                <div class="row">
                    <label for="password2">Повторете новата паролата:</label>
                    <input type="password" name="password2" id="password2" required />
                </div>
                <div class="row row-btn">
                    <input type="hidden" name="token" value="<?= Token::generate('session/pr_token') ?>">
                    <input type="hidden" name="key" value="<?= $data['key'] ?>">
                    <input type="submit" name="submitRegistration" value="Смени паролата ми">
                </div>
                <div class="error-section" style="padding:1rem;">
                    <?= isset($data['status']) ? $data['status'] : '' ?>
                </div>
            </form>
        </div>
    </div>
</div>