<?php

use Application\Utilities\{Token, Functions, Input, Constants};

$today = date('N');
$today--;
$dayOfWeek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
$splits = $errors = $inputs = $edit = $file = null;
$isSplitEdit = Input::keyExists('splitEdit');
$isProfileEdit = (Input::keyExists('action') && Input::get('action') == 'Edit') || isset($data['data']['edit']);
$isDelete = Input::keyExists('action') && Input::get('action') == 'Delete';
$user = $data['user'];
$isAdmin = $data['isAdmin'];

if (isset($data['splits'])) $splits = $data['splits'];
if (isset($data['data']['addErrors'])) $errors = $data['data']['addErrors'];
if (isset($data['data']['addInput'])) $inputs = $data['data']['addInput'];
if (isset($data['data']['tmp_name'])) $file = $data['data']['tmp_name'];
if (Input::keyExists('day')) {
    $day = Input::get('day');
    $today = array_search($day, $dayOfWeek);
}
if ($isSplitEdit) {
    $edit = $splits[$day];
    unset($splits[$day]);
}
?>

<!doctype html>
<html>

<head>
    <title>Fitmissive</title>
    <meta charset="utf-8">
    <meta name="viewport" class="carousel-content">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="/css/common.css" type="text/css">
    <link rel="stylesheet" href="/css/home.css" type="text/css">
    <link rel="stylesheet" href="/css/profile.css" type="text/css">
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
</head>

<body>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <?php require_once 'Application/Views/Common/header.php'; ?>

    <div class="profile-data">
        <?php if ($isDelete) { ?>
            <span style="margin-bottom:10px">Are you sure you want to delete @<?= $user->username ?>?</span>
            <div>
                <form action="/profile/delete" method="post" style="display: inline">
                    <input type="hidden" name="token" value="<?= Token::generate('session/profile_delete_token') ?>">
                    <input type="hidden" name="user_id" value="<?= $user->user_id ?>">
                    <input type="submit" value="Yes">
                </form>
                <form style="display: inline">
                    <input type="submit" value="No">
                </form>
            </div>
        <?php } else { ?>
            <div class="header">
                <img src="<?= $data['picturePath'] ?>" width="70px" height="70px" alt="Profile picture">
                <div class="title">
                    <span>
                        <?php if ($user->fullname != null && !$isProfileEdit) echo $user->fullname;
                        elseif ($isProfileEdit) { ?>
                            <form action="/profile/updateUser" method="post" enctype="multipart/form-data">
                                <input type="text" name="fullname" placeholder="Enter your full name" maxlength="64" value="<?= $user->fullname ?>">
                            <?php } else echo '@' . $user->username ?>
                    </span>
                    <span style="font-size: 10pt">
                        <?php if ($user->fullname != null && !$isProfileEdit) echo '@' . $user->username ?>
                    </span>
                </div>
            </div>
            <div class="body">
                <div>
                    <h1>
                        <?= $data['follows'] ?>
                    </h1>
                    <span>follows</span>
                </div>
                <div style="margin-left: 10px">
                    <h1>
                        <?= $data['followers'] ?>
                    </h1>
                    <span>followers</span>
                </div>
            </div>
            <div class="description-section">
                <?php if ($isProfileEdit) { ?>
                    <textarea name="description" placeholder="Say a couple things about yourself" maxlength="255"><?= $user->description ?></textarea>
                <?php } else { ?>
                    <div class="description"><?= $user->description ?></div>
                <?php } ?>
            </div>
            <div class="bottom">
                <?php if ($user->user_id == $data['loggedUser']) {
                    $form = array('action' => '', 'label' => 'Edit');
                } elseif ($data['isFollowing']) {
                    $form = array('action' => '/profile/follow', 'label' => 'Unfollow');
                } else {
                    $form = array('action' => '/profile/follow', 'label' => 'Follow');
                }
                ?>
                <?php if ($isProfileEdit) { ?>
                    <div class="footer">
                        <input type="hidden" name="token" value="<?= Token::generate('session/profile_edit_token') ?>">
                        <input type="hidden" name="user_id" value="<?= $user->user_id ?>">
                        <input type="hidden" name="username" value="<?= $user->username ?>">
                        <input type="file" name="profilePic" accept="image/png, image/jpg, image/gif, image/jpeg">
                        <input type="submit" value="Save" name="save">
                        </form>
                    </div>
                    <div>
                        <?php
                        if (isset($data['data']['uploadErrors'])) {
                            echo $data['data']['uploadErrors'][0];
                        } else {
                            echo 'The picture must be 1:1 ratio and less than 5MB';
                        }
                        ?>
                    </div>
                <?php } elseif ((!$isSplitEdit && $user->user_id == $data['loggedUser']) || $data['follows'] < Constants::FOLLOWS_MAX) { ?>
                    <form method="post" action="<?= $form['action'] ?>">
                        <input type="hidden" name="userId" value="<?= $user->user_id ?>">
                        <input type="hidden" name="username" value="<?= $user->username ?>">
                        <input type="submit" value="<?= $form['label'] ?>" name="action">
                    </form>
                    <?php if ($data['isAdmin'] && $user->user_id != $data['loggedUser']) { ?>
                        <form method="post">
                            <input type="submit" value="Edit" name="action">
                        </form>
                        <form method="post">
                            <input type="hidden" name="userId" value="<?= $user->user_id ?>">
                            <input type="submit" value="Delete" name="action">
                        </form>
                <?php }
                } ?>
            </div>
        <?php } ?>
    </div>

    <div class="carousel-area">
        <div id="SplitCarousel" class="carousel slide" data-pause="true">
            <ol class="carousel-indicators">
                <?php for ($i = 0; $i < 7; $i++) { ?>
                    <li data-target="#SplitCarousel" data-slide-to="<?php echo $i ?>" class="<?= $i == $today ? 'active' : '' ?>"></li>
                <?php } ?>
            </ol>
            <div class="carousel-inner">
                <?php for ($i = 0; $i < 7; $i++) { ?>
                    <div class="carousel-item <?= $i == $today ? 'active' : '' ?>">
                        <div class="carousel-content">
                            <?php if (isset($splits[$dayOfWeek[$i]])) { ?>
                                <div class="scroller">
                                    <div class="element"><?= $splits[$dayOfWeek[$i]]->description ?></div>
                                </div>
                            <?php } elseif ($user->user_id == $data['loggedUser'] || $isAdmin) { ?>

                                <form action="/profile/updateSplit/<?php echo $dayOfWeek[$i] ?>" method="POST">
                                    <div class="add-form-description">
                                        <textarea class="description-area" id="description" rows="3" name="description" <?php if (!isset($errors)) echo "style='height: 45vh'"; ?> placeholder="Describe your split"><?php
                                                                                                                                                                                                                        if (isset($inputs['description']) && $i == $today) echo Functions::escape($inputs['description']);
                                                                                                                                                                                                                        else if ($isSplitEdit) echo $edit->description;
                                                                                                                                                                                                                        ?></textarea>

                                        <?php
                                        if (isset($errors) && $i == $today) {
                                            include 'Application/Views/Common/error-section.php';
                                        }
                                        ?>
                                    </div>
                                <?php } ?>
                        </div>
                        <div class="carousel-caption d-none d-md-block">
                            <h3><?php echo $dayOfWeek[$i] ?></h3>
                            <?php
                            if ($user->user_id != $data['loggedUser'] && !$isAdmin) { ?>
                                <?php if (isset($splits[$dayOfWeek[$i]])) echo $splits[$dayOfWeek[$i]]->title ?>
                            <?php } elseif (isset($splits[$dayOfWeek[$i]])) { ?>
                                <?= $splits[$dayOfWeek[$i]]->title ?>
                                <?php if (!$isProfileEdit && $isAdmin) { ?>
                                    <form action="" style="margin-top: 10px">
                                        <input type="hidden" name="day" value="<?php echo $dayOfWeek[$i] ?>">
                                        <input type="submit" name="splitEdit" value="Edit">
                                    </form>
                                <?php } ?>
                            <?php } else { ?>

                                <input type="text" class="title-text" name="title" placeholder="Add a title" value="<?php
                                                                                                                    if (isset($inputs['title']) && $i == $today) echo Functions::escape($inputs['title']);
                                                                                                                    else if ($isSplitEdit) echo $edit->title;
                                                                                                                    ?>">
                                <?php if ($isSplitEdit) { ?>
                                    <input type="hidden" name="isEdit" value="true">
                                <?php } ?>
                                <input type="hidden" name="day" value="<?php echo $dayOfWeek[$i] ?>">
                                <input type="hidden" name="user_id" value="<?= $user->user_id ?>">
                                <input type="hidden" name="username" value="<?= $user->username ?>">
                                <input type="hidden" name="token" value="<?php echo Token::generate('session/weekday_tokens/' . $dayOfWeek[$i]); ?>">
                                <input type="submit" class="submit-button" value="Save">
                                </form>

                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <a class="carousel-control-prev" href="#SplitCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#SplitCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>

    <?php require_once 'Application/Views/Common/footer.php'; ?>
</body>

</html>