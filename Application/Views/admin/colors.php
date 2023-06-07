<?php

use Application\Utilities\{Input, Functions};
?>

<div class="panel-container">
    <div class="color-section">
        <i class="fa-solid fa-dumbbell fa-spin fa-lg"></i>
    </div>
    <div class="controls-color">
        <div class="red">
            Червено:<input type="text" name="red" class="red-value" value="00" />
        </div>
        <div class="green">
            Зелено:<input type="text" name="green" class="green-value" value="00" />
        </div>
        <div class="blue">
            Синьо:<input type="text" name="blue" class="blue-value" value="00" />
        </div>
        <div class="result">
            <div id="color-select" class="color-display"></div>
            <form action="/admin/updatecolor" method="POST" id="color-form">
                Резултат: #<input type="text" name="result" class="result-value" maxlength="6"
                    value="<?= Input::keyExists('result') ? Functions::escape(Input::get('result')) : '' ?>" />
                <input type="submit" class="save-btn" value="Запази" style="display: block;margin:auto">
                <div id="error-field"><?= isset($data['error']) ? $data['error'] : '' ?></div>
            </form>
        </div>
    </div>
</div>