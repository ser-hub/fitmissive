<?php
    use Application\Utilities\{Input, Constants, Pagination, Token};

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
    $followToken = Token::generate('session/follow_token');
?>

<?php require_once 'Application/Views/Common/header.php' ?>
<div class="search-content">
    <div class="search-results">
        <?php if (!$results) { ?>
            <div class="search-results-empty">
                <p>Nothing found by '<?= $data['keyword'] ?>'</p>
            </div>
        <?php
        } else { ?>
            <div class="results-wrapper">
                <?php
                foreach ($results as $result) {
                    $userId = $result->user_id;
                ?>
                    <div class="search-results-item" name="search-results-item">
                        <a href="/profile/<?= $result->username ?>">
                            <img src="<?= $pictures[$userId] ?>" width="50px" height="50px" alt="Profile picture">
                            <?= $result->username ?>
                        </a>
                        <?php
                        $condition = $follows && in_array($userId, $follows);
                        $followsCount = $follows ? count($follows) : 0;
                        $targetUsername = $result->username;
                        require 'Application/Views/Common/follow-button.php'
                        ?>
                    </div>
                <?php
                } ?>
            </div>
        <?php
        }
        ?>
        <?php if ($results) { ?>
            <div class="pagination">
                <?php
                $pagination = new Pagination($pages, Constants::PAGINATION_PAGES_TO_SHOW);
                $pagination->setLink('/search/index?search=' . $data['keyword']);
                $pagination->show($currentPage);
                ?>
            </div>
        <?php } ?>
    </div>
</div>
<script src='/Application/js/followButtons.js'></script>
<?php require_once 'Application/Views/Common/footer.php' ?>