<?php

use Application\Utilities\{Token, Functions, Input};

$today = date('w');
$dayOfWeek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
$splits = null;
$errors = null;
$inputs = null;
$edit = null;
$isEdit = Input::keyExists('edit');

if (isset($data['splits'])) $splits = $data['splits'];
if (isset($data['data']['addErrors'])) $errors = $data['data']['addErrors'];
if (isset($data['data']['addInput'])) $inputs = $data['data']['addInput'];
if (isset($inputs['day'])) $today = array_search($inputs['day'], $dayOfWeek) + 1;
if ($isEdit) {
    $day = Input::get('day');
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

    <link rel="stylesheet" href="/css/home.css" type="text/css">
    <link rel="stylesheet" href="/css/common.css" type="text/css">
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
</head>

<body>
    <!-- scripts for bootstrap -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <?php require_once 'Application/Views/Common/header.php'; ?>

    <div class="carousel-area">
        <div id="SplitCarousel" class="carousel slide" data-pause="true">
            <ol class="carousel-indicators">
                <?php for ($i = 0; $i < 7; $i++) { ?>
                    <li data-target="#SplitCarousel" data-slide-to="<?php echo $i ?>" class="<?= $i + 1 == $today ? 'active' : '' ?>"></li>
                <?php } ?>
            </ol>
            <div class="carousel-inner">
                <?php for ($i = 0; $i < 7; $i++) { ?>
                    <div class="carousel-item <?= $i + 1 == $today ? 'active' : '' ?>">
                        <div class="carousel-content">
                            <?php if (isset($splits[$dayOfWeek[$i]])) { ?>
                                <div class="scroller">
                                    <div class="element"><?= $splits[$dayOfWeek[$i]]->description ?></div>
                                </div>
                            <?php } else { ?>

                                <form action="/home/update/<?php echo $dayOfWeek[$i] ?>" method="POST">
                                    <div class="add-form-description">
                                        <textarea class="description-area" id="description" rows="3" name="description" <?php if (!isset($errors)) echo "style='height: 45vh'"; ?> 
                                        placeholder="Describe your split"><?php
                                                                            if (isset($inputs['description']) && $i + 1 == $today) echo Functions::escape($inputs['description']);
                                                                            else if ($isEdit) echo $edit->description;
                                                                            ?></textarea>

                                        <?php
                                        if (isset($errors)) {
                                            require_once 'Application/Views/Common/error-section.php';
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
                                <form action="" style="margin-top: 10px">
                                    <input type="hidden" name="day" value="<?php echo $dayOfWeek[$i] ?>">
                                    <input type="submit" name="edit" value="Edit">
                                </form>
                            <?php } else { ?>

                                <input type="text" class="title-text" name="title" placeholder="Add a title" 
                                value="<?php
                                        if (isset($inputs['title']) && $i + 1 == $today) echo Functions::escape($inputs['title']);
                                        else if ($isEdit) echo $edit->title;
                                        ?>">
                                <?php if ($isEdit) { ?>
                                    <input type="hidden" name="isEdit" value="true">
                                <?php } ?>
                                <input type="hidden" name="token" value="<?php echo Token::generate('session/weekday_tokens/' . $dayOfWeek[$i]); ?>">
                                <!-- <input type="hidden" name="day" value="<?php echo $dayOfWeek[$i] ?>"> -->
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