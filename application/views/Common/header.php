<?php $isLoggedIn = isset($data['loggedUser']) ?>
<header class="headerCust" style="<?php if (!$isLoggedIn) echo 'justify-content: center' ?>">
    <a href="/home"><img src="/img/logo-transparent.png" alt="error" width="400px" height="60px"></a>
    <?php if ($isLoggedIn) { ?>
        <div class="menu">
            <div class="items">
                <form action="/search">
                    <input type="text" class="menu-item" class name="search" placeholder="Search users" style="margin: 8px"
                    value="<?php if (isset($data['keyword'])) echo $data['keyword'] ?>">
                    <input type="submit" class="menu-item" value="Search" style="margin-right:30px">
                </form>
                <form action="/profile">
                    <input type="submit" class="menu-item" value="My profile">
                </form>
                <form action="/home/logout">
                    <input type="submit" class="menu-item" style="margin: 10px" value="Log out">
                </form>
            </div>
        </div>
    <?php } ?>
</header>