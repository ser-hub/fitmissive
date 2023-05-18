<div class="rating-section" name="rating-section" data-token="<?= $ratingToken ?>" data-user=<?= $username ?>>
    <i name="like-btn" class="fa-solid fa-arrow-up fa-<?= $rating === 1 ? 'xl' : 'lg rating-btn'?>"></i>
    <i class="fa-regular" style="padding-right: 1rem"><div class="likes-count"><?= $likes?></div></i>
    <i name="dislike-btn" class="fa-solid fa-arrow-down fa-<?= $rating === 0 ? 'xl' : 'lg rating-btn'?>"></i>
    <i class="fa-regular"><div class="dislikes-count"><?= $dislikes ?></div></i>
</div>