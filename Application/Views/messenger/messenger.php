<div class="messenger-content" style="--main-color: #<?= $data['userColor'] ?>">
    <div class="user-chats">
        <?php foreach ($data['chats'] as $chat) { ?>
            <?php
            if ($chat->getUserA() != $data['sender']) {
                $receiver = $chat->getUserA();
                $receiverPicture = $chat->getUserAPicture();
            } else {
                $receiver = $chat->getUserB();
                $receiverPicture = $chat->getUserBPicture();
            }
            ?>
            <a href="/messenger/<?= $receiver ?>" class="<?php if ($chat->getUnseenMessages()) echo 'notification' ?>">
                <div class="chat-button <?php
                                        if ($receiver === $data['receiver']) echo 'selected';
                                        if ($chat->getUnseenMessages()) echo ' notification'
                                        ?>">
                    <img src="<?= $receiverPicture ?>" class="profile-pic" width="50px" height="50px" alt="Profile picture">
                    <div class="chat-button-text">
                        <div><?= $receiver ?></div>
                        <div></div>
                    </div>
                </div>
            </a>
        <?php } ?>
    </div>

    <?php if (isset($data['receiver'])) { ?>
        <div class="chat-window">
            <div class="chat-header">
                <img src="<?= $data['receiverPic'] ?>" class="profile-pic" height="50" width="50" alt="profile picture" style="margin-right: 10px">
                <div>
                    <?= $data['receiver'] ?>
                    <div class="user-status"></div>
                </div>
                <a href="/profile/<?= $receiver ?>" class="messenger-profile-btn">Виж профила</a>
            </div>
            <div class="messages"></div>
            <input type="hidden" class="sender" value="<?= $data['senderPic'] ?>">
            <input type="hidden" class="receiver" value="<?= $data['receiverPic'] ?>">
            <input type="hidden" class="username" value="<?= $data['sender'] ?>">

            <div class="message-box">
                <input type="text" class="message-field" name="input" spellcheck="false" placeholder="Aa...">
                <button class="message-send-btn" onclick="emitMessage(document.querySelector('.message-field'), socket)">Изпращане</button>
            </div>

        </div>

    <?php } ?>
</div>