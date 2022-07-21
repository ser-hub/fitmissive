<?php $isLoggedIn = isset($data['loggedUser']) ?>
<header class="headerCust" style="<?php if (!$isLoggedIn) echo 'justify-content: center' ?>">
    <a href="/home"><img src="/img/logo-transparent.png" alt="fitmissive" width="400px" height="60px"></a>
    <?php if ($isLoggedIn) { ?>
        <div class="menu">
            <div class="items">
                <form action="/search">
                    <input type="text" class="search-field" name="search" placeholder="Search users" style="margin: 8px"
                        value="<?php if (isset($data['keyword'])) echo $data['keyword'] ?>">
                    <input type="submit" class="menu-item" value="Search" style="margin-right:30px">
                </form>
                <a href="/profile" class="menu-item">My profile</a>
                <a href="/home/logout" class="menu-item" style="margin: 10px">Sign out</a>
            </div>
        </div>
    <?php } ?>
</header>