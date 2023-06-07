<?php

use Application\Utilities\{Token, Functions, Input, Constants};

$today = date('N');
$today--;
$file = null;
$isProfileEdit = (Input::keyExists('action') && Input::get('action') == 'Edit') || isset($data['data']['edit']);
$isDelete = Input::keyExists('action') && Input::get('action') == 'Delete';
$user = $data['user'];
$isAdmin = $data['isAdmin'];
$username = $user->username;
$rating = $data['rating'];
$likes = $data['ratings']['likes'];
$dislikes = $data['ratings']['dislikes'];
$ratingToken = Token::generate('session/rating_token');
$followToken = Token::generate('session/follow_token');

if ($user->description) $user->description = Functions::escape($user->description);
if (isset($data['data']['tmp_name'])) $file = $data['data']['tmp_name'];
?>


<div class="profile-content">
    <div class="profile-data" <?= $isProfileEdit ? 'style="width: auto"' : '' ?>>
        <?php if ($isDelete) { ?>
            <span style="margin: 10px">Сигурли си сте, че искате да изтриете @<?= $username ?>?</span>
            <div class="delete-action">
                <form action="/profile/delete/<?= $username ?>" method="post" class="btn-default">
                    <input type="hidden" name="token" value="<?= Token::generate('session/profile_delete_token') ?>">
                    <input type="submit" value="Да">
                </form>
                <a href="/profile/<?= $username ?>" class="btn-default" style="margin: 10px 10px 0px 0px">Не</a>
            </div>
        <?php } else { ?>
            <div class="header-wrapper" <?= $isProfileEdit ? "style='flex-direction:column; align-items: center'" : '' ?>>
                <img src="<?= $data['picturePath'] ?>" class="profile-pic" width="75px" height="75px" alt="Profile picture" <?= $isProfileEdit ? "style='margin-bottom: 10px'" : '' ?>>
                <div class="header">
                    <div class="title">
                        <?php if ($user->fullname != null && !$isProfileEdit) echo '<span>' . $user->fullname . '</span>';
                        else if ($isProfileEdit) { ?>
                            <form action="/profile/updateUser/<?= $username ?>" method="post" enctype="multipart/form-data">
                                <input type="text" name="fullname" placeholder="Пълно име" maxlength="32" value="<?= $user->fullname ?>">
                                <input type="text" name="email" placeholder="Имейл" maxlength="32" value="<?= $user->email ?>">
                            <?php } ?>
                            <span <?php if ($user->fullname != null) echo "style='font-size: 10pt'" ?>>
                                <?php if (!$isProfileEdit) echo '@' . $username ?>
                            </span>
                    </div>
                    <?= (!$isProfileEdit) && ($user->user_id == $data['loggedUser'] || $isAdmin) ? "<a href='?action=Edit'>Редактирай</a>" : '' ?>
                </div>
            </div>
            <div class="body" style="background-color: #<?= $data['color'] ?>">
                <div class="follows">
                    <h1>
                        <?= $data['follows'] ?>
                    </h1>
                    <p><i>Последвани</i></p>
                </div>
                <div class="follows">
                    <h1>
                        <?= $data['followers'] ?>
                    </h1>
                    <p><i>Последователи</i></p>
                </div>
            </div>
            <div class="description-section">
                <?php if ($isProfileEdit) { ?>
                    <textarea name="description" placeholder="Кажи нещо за себе си" maxlength="500"><?= $user->description ?></textarea>
                <?php } else { ?>
                    <div class="description"><?= $user->description ?></div>
                <?php } ?>
            </div>
            <div>
                <?php if ($isProfileEdit) { ?>
                    <div class="footer">
                        <input type="hidden" name="token" value="<?= Token::generate('session/profile_edit_token') ?>">
                        <input type="file" name="profilePic" accept="image/png, image/jpg, image/gif, image/jpeg">
                        <input type="submit" value="Запази" name="save" class="profile-view-btn">
                        </form>
                        <a href="/profile/<?= $username ?>" class="cancel-btn profile-view-btn">Назад</a>
                    </div>
                    <div style="margin: 5px">
                        <?php
                        if (isset($data['data']['uploadErrors'])) {
                            echo $data['data']['uploadErrors'][0];
                        } else {
                            echo 'The picture must be 1:1 ratio and less than 5MB';
                        }
                        ?>
                    </div>
                <?php } elseif (($user->user_id == $data['loggedUser']) || $data['follows'] < Constants::FOLLOWS_MAX) { ?>
                    <?php
                    if ($user->user_id != $data['loggedUser']) { ?>
                        <div class="profile-data-bottom">
                            <a href="/messenger/<?= $username ?>" class="send-msg-btn btn-default btn-left">Изпрати съобщение</a>
                            <?php
                            $condition = $data['isFollowing'];
                            $followsCount = $data['follows'];
                            $targetUsername = $username;
                            require 'Application/Views/Common/follow-button.php'
                            ?>
                        </div>
                    <?php }
                    if ($data['isAdmin'] && $user->user_id != $data['loggedUser']) { ?>
                        <a href="?action=Delete" class="btn-default delete-btn"><i class="fa-solid fa-user-xmark fa-lg"></i></a>
                <?php }
                } ?>
            </div>
        <?php } ?>
    </div>

    <?php if (!$isProfileEdit) { ?>
        <div class="carousel-area">
            <?php
            require 'Application/Views/Common/workout-carousel-main.php';
            require 'wm-setup-profile.php';
            ?>
            <div class="carousel-bottom">
                <?php require_once 'Application/Views/Common/rating-section.php' ?>
                <div class="colors" data-token="<?= Token::generate('session/color_token') ?>">
                    <?php $cpIndex = 1;
                    foreach ($data['colors'] as $color) { ?>
                        <button class="color-picker" name="cp" data-value="<?= $color ?>" style="background-color: #<?= $color ?>;margin-left:<?= 110 - $cpIndex++ * 19 ?>px"></button>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php
if (!$data['isMyProfile']) echo "<script src='/Application/Views/js/rate.js'></script>";
?>

<script src='/Application/Views/Common/js/follow.js'></script>
<script type="module" src="/node/assets/js/bootstrap.js"></script>