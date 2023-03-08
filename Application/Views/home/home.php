<?php

use Application\Utilities\{Token, Functions, Input};

$today = date('N');
$today--;
$dayOfWeek = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
$splits = $errors = $inputs = $edit = null;
$isEdit = Input::keyExists('edit');
$followedSplits = $data['followedSplits'];

$ratingToken = Token::generate('session/rating_token');

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

<?php require_once 'Application/Views/Common/header.php' ?>

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
        <button class="carousel-control-prev" type="button" data-bs-target="#SplitCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#SplitCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <hr style="margin-top:5vh">

    <?php
    $currentCarousel = 0;
    foreach ($followedSplits as $user_id => $split) {
    ?>
        <div id="FollowedSplitCarousel<?= $currentCarousel ?>" class="carousel slide" data-bs-ride="false">
            <div class="carousel-indicators">
                <?php for ($i = 0; $i < 7; $i++) { ?>
                    <button data-bs-target="#FollowedSplitCarousel<?= $currentCarousel ?>" data-bs-slide-to="<?php echo $i ?>" <?= $i == $today ? "class='active' aria-current='true'" : '' ?> aria-label="<?php echo $dayOfWeek[$i] ?> slide"></button>
                <?php } ?>
            </div>
            <div class="carousel-inner">
                <?php for ($i = 0; $i < 7; $i++) { ?>
                    <div class="carousel-item <?= $i == $today ? 'active' : '' ?>">
                        <div class="carousel-content">
                            <div class="scroller">
                                <div class="element"><?= isset($split[$dayOfWeek[$i]]->description) ? $split[$dayOfWeek[$i]]->description : "" ?></div>
                            </div>
                        </div>
                        <div class="carousel-caption d-none d-md-block">
                            <h3><?= $user_id . '\'s ' . $dayOfWeek[$i] ?></h3>
                            <?= isset($split[$dayOfWeek[$i]]->title) ? $splits[$dayOfWeek[$i]]->title : "" ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#FollowedSplitCarousel<?= $currentCarousel ?>" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#FollowedSplitCarousel<?= $currentCarousel ?>" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <div class="ratings-wrapper">
            <?php
            $username = $split['username'];
            $rating = $split['rating'];
            $likes = $split['ratings']['likes'];
            $dislikes = $split['ratings']['dislikes'];
            require 'Application/Views/Common/rating-section.php'
            ?>
        </div>
    <?php
        $currentCarousel++;
    }
    ?>
</div>

<script src='/Application/js/rateButtons.js'></script>
<script type="module" src="/node/assets/js/bootstrap.js"></script>
<?php require_once 'Application/Views/Common/footer.php' ?>