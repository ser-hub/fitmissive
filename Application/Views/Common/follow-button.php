<?php
use Application\Utilities\Constants;

if ($followsCount < Constants::FOLLOWS_MAX) { ?>
    <div class="btn-default" name="follow-btn" data-token="<?= $followToken ?>" data-target="<?= $targetUsername ?>">
        <?= $condition ? 'Unfollow' : 'Follow' ?>
    </div>
<?php } ?>