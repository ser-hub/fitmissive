<?php

use Application\Utilities\{Input, Functions};

$errorMode = isset($data['error']);
?>

<div class="panel-container">
    <div class="selector-section">
        <form action="/admin/newexercise" method="POST">
            <select class="category-select" name="exercise-category"
                data-selected="<?= Input::keyExists('exercise-category') ? Functions::escape(Input::get('exercise-category')) : '' ?>">
            </select>
            <div class="options">
                <i class="fa-solid fa-dumbbell fa-spin fa-lg" style="margin:0 2rem"></i>
            </div>
            <div class="plus-btn option-selected"><i class="fa-solid fa-square-plus"></i></div>
    </div>
    <div class="controls">
        <div class="form-title" style="margin:.5rem">Добвяне на упражнение</div>
        <div class="row">
            <label for="exercise-title">Заглавие:</label>
            <input type="text" name="exercise-title" id="exercise-title" placeholder="Makc: 100" maxlength="100"
                value="<?= Input::keyExists('exercise-title') ? Functions::escape(Input::get('exercise-title')) : '' ?>" />
        </div>
        <div class="row">
            <label for="exercise-description">Описание:</label>
            <textarea name="exercise-description" rows="7rem" id="exercise-description" placeholder="Макс: 1000" maxlength="1000">
<?= Input::keyExists('exercise-description') ? Functions::escape(Input::get('exercise-description')) : '' ?></textarea>
        </div>
        <div class="row row-btn">
            <input type="submit" class="save-btn" value="Добави">
            <a href="/admin/deleteexercise" class="save-btn" style="display:none; margin-left: 5px;">Изтрий</a>
        </div>
            <div id="error-field"><?= $errorMode ? $data['error'] : '' ?></div>
        </form>
    </div>
</div>