<?php

use Application\Utilities\Token;

$dayOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$dayOfWeekDisplay = ['Понеделник', 'Вторник', 'Сряда', 'Четвъртък', 'Петък', 'Събота', 'Неделя'];

$splits = null;
if (isset($data['workout'])) $splits = $data['workout'];
?>

<div id="SplitCarousel" class="carousel slide" style="margin-top:15px">
    <div class="carousel-indicators">
        <?php for ($i = 0; $i < 7; $i++) { ?>
            <button data-bs-target="#SplitCarousel" data-bs-slide-to="<?php echo $i ?>" <?= $i == $today ? "class='active' aria-current='true'" : '' ?> aria-label="<?php echo $dayOfWeek[$i] ?> slide"></button>
        <?php } ?>
    </div>
    <div class="carousel-inner" style="background-color: #<?= $data['color'] ?>">
        <?php for ($i = 0; $i < 7; $i++) { ?>
            <div class="carousel-item <?= $i == $today ? 'active' : '' ?>">
                <div class="carousel-content">
                    <div class="scroller" name="workout-container" data-day="<?= $dayOfWeek[$i] ?>" data-token="<?= Token::generate('session/weekday_tokens/' . $dayOfWeek[$i]) ?>">
                        <?= isset($splits[$dayOfWeek[$i]]) ? $splits[$dayOfWeek[$i]]->description : "" ?>
                            <i class="fa-solid fa-dumbbell fa-spin fa-lg" style="margin-left: 1rem"></i>  
                    </div>
                </div>
                <div class="carousel-caption d-none d-md-block">
                    <?= isset($splits[$dayOfWeek[$i]]) ? "<h3>" . $splits[$dayOfWeek[$i]]->title . '</h3>' . $dayOfWeekDisplay[$i] : $dayOfWeekDisplay[$i] ?>
                </div>
            </div>
        <?php } ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#SplitCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Предишна</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#SplitCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Следваща</span>
    </button>
</div>