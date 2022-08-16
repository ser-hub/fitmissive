<?php

use Application\Utilities\{Token, Functions, Input};

$today = date('N');
$today--;
$dayOfWeek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
$splits = $errors = $inputs = $edit = null;
$isEdit = Input::keyExists('edit');
$followedSplits = $data['followedSplits'];

if (isset($data['splits'])) $splits = $data['splits'];
if (isset($data['data']['updateErrors'])) $errors = $data['data']['updateErrors'];
if (isset($data['data']['updateInput'])) $inputs = $data['data']['updateInput'];
if (Input::keyExists('day')) {
    $day = Input::get('day');
    $today = array_search($day, $dayOfWeek);
} elseif (isset($inputs['day'])) {
    $day = $inputs['day'];
    $today = array_search($day, $dayOfWeek);
}
if ($isEdit) {
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

    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
</head>

<body>
    <?php require_once 'Application/Views/Common/header.php' ?>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

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
                            <?php } else { ?>

                                <form action="/home/update/<?php echo $dayOfWeek[$i] ?>" method="POST">
                                    <div class="add-form-description">
                                        <textarea class="description-area" id="description" rows="3" name="description" <?php if (!isset($errors)) echo "style='height: 45vh'"; ?> placeholder="Describe your split" maxlength="800"><?php
                                                                                                                                                                                                                                        if (isset($inputs['description']) && $i == $today) echo Functions::escape($inputs['description']);
                                                                                                                                                                                                                                        else if ($isEdit) echo $edit->description;
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
                            if (isset($splits[$dayOfWeek[$i]])) { ?>
                                <?= $splits[$dayOfWeek[$i]]->title ?>
                                <form action="/home" style="margin-top: 10px">
                                    <input type="hidden" name="day" value="<?php echo $dayOfWeek[$i] ?>">
                                    <input type="submit" name="edit" value="Edit">
                                </form>
                            <?php } else { ?>

                                <input type="text" class="title-text" name="title" placeholder="Add a title" maxlength="45" value="<?php
                                                                                                                                    if (isset($inputs['title']) && $i == $today) echo Functions::escape($inputs['title']);
                                                                                                                                    else if ($isEdit) echo $edit->title;
                                                                                                                                    ?>">
                                <?php if ($isEdit) { ?>
                                    <input type="hidden" name="isEdit" value="true">
                                <?php } ?>
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

        <?php
        $i = 0;
        foreach ($followedSplits as $split) {
        ?>
            <div id="FollowedSplitCarousel<?= $i++ ?>" class="carousel slide" data-pause="true" style="margin-top: 5vh">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="carousel-content">
                            <div class="scroller">
                                <div class="element"><?= $split->description ?></div>
                            </div>
                        </div>
                        <div class="carousel-caption d-none d-md-block">
                            <h3><?= $split->user_id . '\'s ' . $dayOfWeek[$today] ?></h3>
                            <?= $split->title ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php require_once 'Application/Views/Common/footer.php' ?>
</body>

</html>