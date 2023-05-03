<?php
    use Application\Utilities\{Input, Token, Constants};

    $slug = explode('/', Input::get('url'));
    $slug = end($slug);
    $errors = $data['errors'];
    if (!$data['adminMode'] && !strlen($data['content'] && $data['info'])) {
        $data['title'] = $data['info'][0]->title;
        $data['content'] = $data['info'][0]->content;
    }
    $inputs = null;
    if ($data['inputs'] != null) {
        $inputs = $data['inputs'];
    }
?>

<?php require_once 'Application/Views/Common/header.php' ?>
<div class="info-grid">
    <div class="info-area">
        <?php if (Input::keyExists('action') && Input::get('action') == 'edit') { ?>
            <?php if ($errors != null) include 'Application/Views/Common/error-section.php' ?>
            <form action="/info/update/<?= $slug ?>" method="POST">
                <input type="hidden" name="title" value="<?= $data['title'] ?>">
                <input type="hidden" name="token" value="<?= Token::generate('session/info_update_token') ?>">
                <textarea name="content" maxlength="5001"><?php
                                                            if (isset($data['content'])) echo $data['content'];
                                                            else if (isset($data['inputs']['content'])) echo $data['inputs']['content'] ?></textarea>
                <input type="submit" value="Save">
            </form>
        <?php } elseif (Input::keyExists('action') && Input::get('action') == 'delete') { ?>
            Are you sure you want to delete '<?= $data['title'] ?>'?
            <div class='bottom'>
                <form action="/info/delete/<?php foreach ($data['info'] as $info) {
                                                if ($info->slug == $slug) {
                                                    echo $info->info_id;
                                                }
                                            } ?>" method="POST" class="yes-no">
                    <input type="hidden" name="token" value="<?= Token::generate('session/info_delete_token') ?>">
                    <input type="submit" value="Yes" style="margin-right: 10px">
                    <a href="/info/<?= $slug ?>" class="menu-item">No</a>
                </form>
            </div>
        <?php } elseif ((Input::keyExists('action') && Input::get('action') == 'create') || $errors != null) { ?>
            <?php if ($errors != null) include 'Application/Views/Common/error-section.php' ?>
            <form action="/info/create" method="POST">
                <input type="text" name="title" maxlength="45" placeholder="Заглавие" required value="<?php if ($inputs != null) echo $inputs['title'] ?>">
                <div style="margin: 10px">
                    <input type="text" name="slug" maxlength="45" placeholder="Slug" required value="<?php if ($inputs != null) echo $inputs['slug'] ?>">
                </div>
                <textarea name="content" maxlength="1000" placeholder="Content" required><?php if ($inputs != null) echo $inputs['content'] ?></textarea>
                <input type="hidden" name="token" value="<?= Token::generate('session/info_create_token') ?>">
                <input type="submit" class="btn-default" value="Запамети">
                <a href="/info" class="menu-item" style="margin-top: 10px">Назад</a>
            </form>
            <?php } else {
            if ($data['adminMode']) { ?>
                <div class='top'>
                    <a href="/info" class="menu-item">Инофмационен панел</a>
                </div>
            <?php }
            if (isset($data['content']) && strlen($data['content'])) { ?>
                <span class="info-title"><?= isset($data['title']) ? $data['title'] : '' ?></span>
                <span class="info-text"><?= trim($data['content']) ?></span>
                <?php } elseif ($data['adminMode']) {
                echo "<div class='infos-menu'>";
                foreach ($data['info'] as $info) { ?>
                    <a href="/info/<?= $info->slug ?>"><?= $info->title ?></a><br>
                <?php }
                echo '</div>';
            } else {
                echo 'Нищо не е намерено';
            }
            if ($data['adminMode'] && $data['title']) { ?>
                <div class='bottom'>
                    <a href="/info/<?= $slug ?>?action=edit" style="border-right: 2px solid black">Редактирай</a>
                    <a href="/info/<?= $slug ?>?action=delete">Изтрий</a>
                </div>
            <?php } elseif ($data['adminMode'] && count($data['info']) < Constants::INFO_MAX) { ?>
                <div class='bottom'>
                    <a href="/info?action=create">Добави</a>
                </div>
        <?php }
        } ?>
    </div>
</div>
<?php require_once 'Application/Views/Common/footer.php' ?>