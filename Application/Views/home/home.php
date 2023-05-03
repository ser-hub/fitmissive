<?php
    use Application\Utilities\{Token, Input};

    $today = date('N');
    $today--;
    $followedSplits = $data['followedSplits'];

    $ratingToken = Token::generate('session/rating_token');
    $weekDayTokens = [];
?>

<?php require_once 'Application/Views/Common/header.php' ?>
<div class="carousel-area">
    <div class="feed-wrapper">
        <?php require_once 'Application/Views/Common/workout-carousel-main.php' ?>

        <div class="hr"></div>

        <?php
        $currentCarousel = 0;
        foreach ($followedSplits as $userId => $split) {
        ?>
            <div class="followed-split">
                <div id="FollowedSplitCarousel<?= $currentCarousel ?>" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-indicators">
                        <?php for ($i = 0; $i < 7; $i++) { ?>
                            <button data-bs-target="#FollowedSplitCarousel<?= $currentCarousel ?>" data-bs-slide-to="<?php echo $i ?>" <?= $i == $today ? "class='active' aria-current='true'" : '' ?> aria-label="<?php echo $dayOfWeek[$i] ?> slide"></button>
                        <?php } ?>
                    </div>
                    <div class="carousel-inner" style="background-color: #<?= $split['color'] ?>">
                        <?php for ($i = 0; $i < 7; $i++) { ?>
                            <div class="carousel-item <?= $i == $today ? 'active' : '' ?>">
                                <div class="carousel-content">
                                    <div class="scroller" name="<?= $dayOfWeek[$i] ?>">
                                        <?= isset($split[$dayOfWeek[$i]]->description) ? $split[$dayOfWeek[$i]]->description : "" ?>
                                    </div>
                                </div>
                                <div class="carousel-caption d-none d-md-block">
                                    <h3><?= isset($split[$dayOfWeek[$i]]->title) ? $split[$dayOfWeek[$i]]->title : "" ?></h3>
                                    <?= $dayOfWeekDisplay[$i] ?> <br>
                                    <?= isset($split[$dayOfWeek[$i]]->last_updated) ? $split[$dayOfWeek[$i]]->last_updated : '' ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#FollowedSplitCarousel<?= $currentCarousel ?>" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Предишна</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#FollowedSplitCarousel<?= $currentCarousel ?>" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Следваща</span>
                    </button>
                </div>
                <div class="carousel-bottom">
                    <img src="<?= $split['userPicture'] ?>" class="profile-pic" width="50px" height="50px" alt="Profile picture">
                    <?= $userId ?>
                    <?php
                    $username = $split['username'];
                    $rating = $split['rating'];
                    $likes = $split['ratings']['likes'];
                    $dislikes = $split['ratings']['dislikes'];
                    require 'Application/Views/Common/rating-section.php'
                    ?>
                </div>
            </div>
        <?php
            $currentCarousel++;
        }
        ?>
    </div>
</div>

<script src='/Application/js/rateButtons.js'></script>
<script type="module" src="/node/assets/js/bootstrap.js"></script>
<?php require_once 'Application/Views/Common/footer.php' ?>