<div class="messenger-content">
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
                <img src="<?= $data['receiverPic'] ?>" class="profile-pic" height="40" width="40" alt="profile picture" style="margin-right: 10px">
                <?= $data['receiver'] ?>
                <a href="/profile/<?= $receiver ?>" class="messenger-profile-btn">Виж профила</a>
            </div>
            <div class="messages">
                <?php if (!empty($data['messages'])) { ?>
                    <?php $currentAuthor = null ?>
                    <?php foreach ($data['messages'] as $message) { ?>
                        <?php if ($message->user_id !== $currentAuthor) { ?>
                            <?php if ($currentAuthor != null) { ?>
            </div>
        </div>
    <?php } ?>
    <div class="chat-item" <?= $message->user_id == $data['sender'] ? "style='margin-left: auto'" : '' ?>>
        <div style="margin-right: 10px;">
            <img src="<?php
                            if ($message->user_id == $data['sender']) {
                                echo $data['senderPic'];
                            } else {
                                echo $data['receiverPic'];
                            }
                        ?>" class="profile-pic" height="30" width="30" alt="profile picture">
        </div>
        <div>
            <div class="author"><?= $message->user_id ?></div>
            <div class="message"><?= $message->message ?></div>
        <?php
                            $currentAuthor = $message->user_id;
                        } else { ?>
            <div class="message"><?= $message->message ?></div>
        <?php } ?>
    <?php } ?>
        </div>
    </div>
<?php } else { ?>
    <div class="prompt">
        Кажи здрасти на <?= $data['receiver'] ?>
    </div>
<?php } ?>
</div>
<input type="hidden" class="sender" value="<?= $data['senderPic'] ?>">
<input type="hidden" class="receiver" value="<?= $data['receiverPic'] ?>">
<input type="hidden" class="username" value="<?= $data['sender'] ?>">

<div class="message-box">
    <input type="text" class="message-field" name="input" spellcheck="false" placeholder="Aa...">
    <button class="message-send-btn" onclick="emitMessage(document.querySelector('.message-field'), socket)">Изпращане</button>
</div>

</div>

<?php include "Application/Views/js/Socket/chat.php" ?>

<?php } ?>
</div>