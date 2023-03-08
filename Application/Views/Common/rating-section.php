<div class="rating-section" name="rating-section" data-token="<?= $ratingToken ?>" data-user=<?= $username ?>>
    <i name="like-btn" class="fa-<?php if ($rating === 1) echo 'solid';
                                else echo 'regular';
                                ?> fa-thumbs-up fa-xl" style="padding-right: 5px"></i>
    <i class="fa-regular" style="padding-right: 25px"><i class="likes-count"><?= $likes?></i></i>
    <i name="dislike-btn" class="fa-<?php if ($rating === 0) echo 'solid';
                                    else echo 'regular';
                                    ?> fa-thumbs-down fa-xl" style="padding-right: 5px"></i>
    <i class="fa-regular"><i class="dislikes-count"><?= $dislikes ?></i></i>
</div>