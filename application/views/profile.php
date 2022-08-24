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

<?php require_once 'Application/Views/Common/header.php' ?>

<div class="profile-data">
    <?php if ($isDelete) { ?>
        <span style="margin-bottom:10px">Are you sure you want to delete @<?= $user->username ?>?</span>
        <div>
            <form action="/profile/delete/<?= $user->username ?>" method="post" style="display: inline">
                <input type="hidden" name="token" value="<?= Token::generate('session/profile_delete_token') ?>">
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
                        <form action="/profile/updateUser/<?= $user->username ?>" method="post" enctype="multipart/form-data">
                            <input type="text" name="fullname" placeholder="Enter your full name" maxlength="32" value="<?= $user->fullname ?>">
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
                $form = array('action' => '/profile/follow/' . $user->username, 'label' => 'Unfollow');
            } else {
                $form = array('action' => '/profile/follow/' . $user->username, 'label' => 'Follow');
            }
            ?>
            <?php if ($isProfileEdit) { ?>
                <div class="footer">
                    <input type="hidden" name="token" value="<?= Token::generate('session/profile_edit_token') ?>">
                    <input type="file" name="profilePic" accept="image/png, image/jpg, image/gif, image/jpeg">
                    <input type="submit" value="Save" name="save">
                    </form>
                    <a href="/profile/<?= $user->username ?>" class="menu-item" style="margin-left: 5px">Cancel</a>
                </div>
                <div style="margin-top: 5px">
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
                    <input type="submit" value="<?= $form['label'] ?>" name="action">
                </form>
                <?php if ($data['isAdmin'] && $user->user_id != $data['loggedUser']) { ?>
                    <a href="?action=Edit" class="menu-item">Edit</a>
                    <a href="?action=Delete" class="menu-item">Delete</a>
                <?php }
                if ($user->user_id != $data['loggedUser']) { ?>
                    <div style="padding: 10px">
                        <a href="/messenger/<?= $user->username ?>" class="menu-item">Send a message</a>
                    </div>
            <?php }
            } ?>
        </div>
    <?php } ?>
</div>

<div class="carousel-area">
    <div id="SplitCarousel" class="carousel slide" data-bs-ride="false">
        <div class="carousel-indicators">
            <?php for ($i = 0; $i < 7; $i++) { ?>
                <button data-bs-target="#SplitCarousel" data-bs-slide-to="<?php echo $i ?>" <?= $i == $today ? "class='active' aria-current='true'" : '' ?> aria-label="<?php echo $dayOfWeek[$i] ?> slide"></button>
            <?php } ?>
        </div>
        <div class="carousel-inner">
            <?php for ($i = 0; $i < 7; $i++) { ?>
                <div class="carousel-item <?= $i == $today ? 'active' : '' ?>">
                    <div class="carousel-content">
                        <?php if (isset($splits[$dayOfWeek[$i]])) { ?>
                            <div class="scroller">
                                <div class="element"><?= $splits[$dayOfWeek[$i]]->description ?></div>
                            </div>
                        <?php } elseif ($user->user_id == $data['loggedUser'] || $isAdmin) { ?>

                            <form action="/profile/updateSplit/<?= $user->username ?>/<?= $dayOfWeek[$i] ?>" method="POST">
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

                            <input type="text" class="title-text" name="title" placeholder="Add a title" maxlength="45" value="<?php
                                                                                                                                if (isset($inputs['title']) && $i == $today) echo Functions::escape($inputs['title']);
                                                                                                                                else if ($isSplitEdit) echo $edit->title;
                                                                                                                                ?>">
                            <?php if ($isSplitEdit) { ?>
                                <input type="hidden" name="isEdit" value="true">
                            <?php } ?>
                            <input type="hidden" name="day" value="<?php echo $dayOfWeek[$i] ?>">
                            <input type="hidden" name="token" value="<?php echo Token::generate('session/weekday_tokens/' . $dayOfWeek[$i]); ?>">
                            <input type="submit" class="submit-button" value="Save">
                            </form>

                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#SplitCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#SplitCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<script type="module" src="/node/assets/js/bootstrap.js"></script>
<?php require_once 'Application/Views/Common/footer.php' ?>