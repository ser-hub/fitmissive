<?php

use Application\Utilities\{Input, Constants, Pagination};

$results = $pages = null;
if ($data['searchResults']) {
    $results = $data['searchResults']['users'];
    $pages = ceil($data['searchResults']['total'] / Constants::PAGINATION_SEARCH_RESULTS_PER_PAGE);
}
$pictures = $data['profilePictures'];
$follows = $data['follows'];
$currentPage = 1;
if (Input::keyExists('page') && Input::get('page') > 0) {
    $currentPage = Input::get('page');
}

?>

<?php require_once 'Application/Views/Common/header.php' ?>
<div class="search-results">
    <?php if (!$results) { ?>
        <div class="search-results-empty">
            <p>Nothing found by '<?= $data['keyword'] ?>'</p>
        </div>
        <?php
    } else {
        foreach ($results as $result) {
            $userId = $result->user_id;
        ?>
            <div class="search-results-item">
                <a href="/profile/<?= $result->username ?>">
                    <img src="<?= $pictures[$userId] ?>" width="50px" height="50px" alt="Profile picture">
                    <?= $result->username ?>
                </a>
                <?php if (!$follows || count($follows) < Constants::FOLLOWS_MAX) { ?>
                    <div class="follow-btn">
                        <form action="/search/follow" method="POST">
                            <input type="hidden" name="followed" value="<?= $userId ?>">
                            <input type="submit" value="<?php
                                                        if ($follows && in_array($userId, $follows))
                                                            echo 'Unfollow';
                                                        else
                                                            echo 'Follow'; ?>" name="action">
                        </form>
                    </div>
                <?php } ?>
            </div>
    <?php
        }
    }
    ?> 
    <div class="pagination">
        <?php
            $pagination = new Pagination($pages, Constants::PAGINATION_PAGES_TO_SHOW);
            $pagination->setLink('/search/index?search='.$data['keyword']);
            $pagination->show($currentPage);
        ?>
    </div>
</div>
<?php require_once 'Application/Views/Common/footer.php' ?>