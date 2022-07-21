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

<!doctype html>
<html>

<head>
    <title>Fitmissive</title>
    <link rel="stylesheet" href="/css/common.css" type="text/css">
    <link rel="stylesheet" href="/css/home.css" type="text/css">
    <link rel="stylesheet" href="/css/info.css" type="text/css">
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
</head>

<body>
    <?php require_once 'Application/Views/Common/header.php'; ?>
    <div class="info-area">
        <?php if (Input::keyExists('action') && Input::get('action') == 'edit') { ?>
            <form action="/info/update/<?= $slug ?>" method="POST">
                <input type="hidden" name="title" value="<?= $data['title'] ?>">
                <input type="hidden" name="token" value="<?= Token::generate('session/info_update_token') ?>">
                <textarea name="content" maxlength="1000"><?= $data['content'] ?></textarea>
                <input type="submit" value="Save">
            </form>
        <?php } elseif (Input::keyExists('action') && Input::get('action') == 'delete') { ?>
            Are you sure you want to delete '<?= $data['title'] ?>'?
            <div class='bottom'>
                <form action="/info/delete/<?= $slug ?>" method="POST" class="yes-no">
                    <input type="hidden" name="token" value="<?= Token::generate('session/info_delete_token') ?>">
                    <input type="submit" value="Yes" style="margin-right: 10px">
                    <a href="/info/<?= $slug ?>" class="menu-item">No</a>
                </form>
            </div>
        <?php } elseif ((Input::keyExists('action') && Input::get('action') == 'create') || $errors != null) { ?>
            <?php if ($errors != null) include 'Application/Views/Common/error-section.php'?>
            <form action="/info/create" method="POST">
                <input type="text" name="title" maxlength="45" placeholder="Title" required value="<?php if ($inputs != null) echo $inputs['title']?>">
                <div style="margin: 10px">
                    <input type="text" name="slug" maxlength="45" placeholder="Slug" required value="<?php if ($inputs != null) echo $inputs['slug']?>">
                </div>
                <textarea name="content" maxlength="1000" placeholder="Content" required><?php if ($inputs != null) echo $inputs['content']?></textarea>
                <input type="hidden" name="token" value="<?= Token::generate('session/info_create_token') ?>">
                <input type="submit" value="Save">
            </form>
            <?php } else {
            if (isset($data['content']) && strlen($data['content'])) { ?>
                <?= $data['content'] ?>
                <?php if ($data['adminMode']) { ?>
                    <div class='top'>
                        <a href="/info" class="menu-item">Info panel</a>
                    </div>
                    <div class='bottom'>
                        <a href="/info/<?= $slug ?>?action=edit" class="menu-item" style="margin-right: 10px">Edit</a>
                        <a href="/info/<?= $slug ?>?action=delete" class="menu-item">Delete</a>
                    </div>
                <?php }
            } elseif ($data['adminMode']) {
                foreach ($data['info'] as $info) { ?>
                    <a href="/info/<?= $info->slug ?>"><?= $info->title ?></a><br>
                <?php }
                if (count($data['info']) < Constants::INFO_MAX) { ?>
                    <div class="bottom">
                        <a href="/info?action=create" class="menu-item">Add info</a>
                    </div>
        <?php }
            } else {
                echo 'No info found';
            }
        } ?>
    </div>
    <?php require_once 'Application/Views/Common/footer.php'; ?>
</body>

</html>