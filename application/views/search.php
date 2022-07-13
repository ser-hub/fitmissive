<?php

use Application\utilities\Functions;

$results = $data['searchResults'];
$follows = $data['follows'];
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
    <?php require_once 'Application/Views/Common/header.php'; ?>
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
                    <a href="/profile/index/<?= $result->username ?>">
                        <img src="<?= Functions::getProfilePicPath($userId) ?>" width="50px" height="50px" alt="Profile picture">
                        <?= $result->username ?>
                    </a>
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
                </div>
        <?php
            }
        }
        ?>
    </div>
    <?php require_once 'Application/Views/Common/footer.php'; ?>
</body>

</html>