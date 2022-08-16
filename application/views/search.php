<?php

use Application\Utilities\{Input, Constants};

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

<!doctype html>
<html>

<head>
    <title>Fitmissive</title>
    <link rel="stylesheet" href="/css/common.css" type="text/css">
    <link rel="stylesheet" href="/css/search.css" type="text/css">
    <link rel="icon" href="data:;base64,iVBORw0KGgo=">
</head>

<body>
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
            if ($pages > 10) {
                for ($i = 1; $i <= Constants::PAGINATION_PAGES_TO_SHOW; $i++) {
                    if ($i == $currentPage) echo "<span class='selected'>"?>
                    <a href="/search/index?search=<?= $data['keyword'] ?>&page=<?= $i ?>"><?= $i ?></a><?php if ($i != $pages) echo ' | ' ?>
                    <?php if ($i == $currentPage) echo "</span>"?>
                <?php    } ?>
                <?php
                if ($currentPage > Constants::PAGINATION_PAGES_TO_SHOW + 3) {
                    echo '... |';
                }
                for ($i = 0; $i < 5; $i++) { 
                    if (($currentPage - 2) + $i > Constants::PAGINATION_PAGES_TO_SHOW && ($currentPage - 2) + $i < $pages - Constants::PAGINATION_PAGES_TO_SHOW) {
                    if (($currentPage - 2) + $i == $currentPage) echo "<span class='selected'>"?>
                    <a href="/search/index?search=<?= $data['keyword'] ?>&page=<?= ($currentPage - 2) + $i ?>"><?= ($currentPage - 2) + $i ?></a><?php if (($currentPage - 2) + $i != $pages) echo ' | ' ?>
                    <?php if (($currentPage - 2) + $i == $currentPage) echo "</span>"?>
                <?php }
                }

                if ($currentPage < $pages - (Constants::PAGINATION_PAGES_TO_SHOW + 3)) {
                    echo '... |';
                }
                ?>
                    <?php for ($i = $pages - Constants::PAGINATION_PAGES_TO_SHOW + 1; $i <= $pages; $i++) {
                    if ($i == $currentPage) echo "<span class='selected'>"?>
                    <a href="/search/index?search=<?= $data['keyword'] ?>&page=<?= $i ?>"><?= $i ?></a><?php if ($i != $pages) echo ' | ' ?>
                    <?php if ($i == $currentPage) echo "</span>"?>
                <?php    } ?>
            <?php } else {
                for ($i = 1; $i <= $pages; $i++)  {
                    if ($i == $currentPage) echo "<span class='selected'>";?>
                    <a href="/search/index?search=<?= $data['keyword'] ?>&page=<?= $i ?>"><?= $i ?></a>
                <?php if ($i == $currentPage) echo "</span>"; ?>
                <?php if ($i != $pages) echo ' | ' ?>
                <?php
                 } 
                }?>
        </div>
    </div>
    <?php require_once 'Application/Views/Common/footer.php' ?>
</body>

</html>