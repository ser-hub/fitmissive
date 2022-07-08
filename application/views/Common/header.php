<?php $isLoggedIn = isset($data['loggedUser']) ?>
<header class="headerCust" style="<?php if (!$isLoggedIn) echo 'justify-content: center' ?>">
    <a href="/home"><img src="/img/logo-transparent.png" alt="error" width="400px" height="60px"></a>
    <?php if ($isLoggedIn) { ?>
        <div class="menu">
            <div class="items">
                <form action="/home/search">
                    <input type="text" name="search" placeholder="Search users" style="margin: 8px">
                    <input type="submit" value="Search" style="margin-right:30px">
                </form>
                <form action="/home" method="POST">
                    <input type="submit" style="margin-right: 10px" value="My splits">
                </form action="/home/profile">
                <form>
                    <input type="submit" value="My profile">
                </form>
                <form action="/home/logout">
                    <input type="submit" style="margin: 10px" value="Log out">
                </form>
            </div>
        </div>
    <?php } ?>
</header>