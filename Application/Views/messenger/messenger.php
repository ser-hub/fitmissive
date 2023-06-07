<div class="messenger-content" style="--main-color: #<?= $data['userColor'] ?>">
    <div class="user-chats">
        <a href="/messenger">
        <div class="chat-notify"></div>
        </a>
        <?php if (empty($data['chats'])) { ?>
            <div class="empty-message">
                Чатовете ти ще се появяват тук
            </div>
            <?php } else {
            foreach ($data['chats'] as $chat) { ?>
                <?php
                if ($chat->getUserA() != $data['sender']) {
                    $receiver = $chat->getUserA();
                    $receiverPicture = $chat->getUserAPicture();
                } else {
                    $receiver = $chat->getUserB();
                    $receiverPicture = $chat->getUserBPicture();
                }
                ?>
                <a href="/messenger/<?= $receiver ?>">
                    <div class="chat-button <?= $receiver === $data['receiver'] ? 'selected' : '' ?>">
                        <img src="<?= $receiverPicture ?>" class="profile-pic" width="50px" height="50px" alt="Profile picture">
                        <div class="chat-button-text">
                            <div><?= $receiver ?></div>
                            <div class="messages-notify">
                                <?php
                                if ($receiver != $data['receiver']) {
                                    $unseenMessages = $chat->getUnseenMessages();
                                    if ($unseenMessages == 1) {
                                        echo $unseenMessages . ' ново съобщение';
                                    } elseif ($unseenMessages > 1) {
                                        echo $unseenMessages . ' нови съобщения';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </a>
        <?php }
        } ?>
    </div>

    <?php if (isset($data['receiver'])) { ?>
        <div class="chat-window">
            <div class="chat-header">
                <img 
                    src="<?= $data['selectedUserPicture'] ?>" 
                    class="profile-pic" 
                    height="50" 
                    width="50" 
                    alt="profile picture" 
                    style="margin-right: 10px;border-color: #<?= $data['selectedUserColor'] ?>">
                <div>
                    <?= $data['receiver'] ?>
                    <div class="user-status"></div>
                </div>
                <a href="/profile/<?= $receiver ?>" class="messenger-profile-btn">Виж профила</a>
            </div>
            <div class="messages">
                <div class="loading-icon">
                    <i class="fa-solid fa-dumbbell fa-spin fa-lg" style="margin:auto"></i>
                </div>
            </div>

            <div class="message-box">
                <input type="text" class="message-field" name="input" spellcheck="false" placeholder="Aa...">
                <button class="message-send-btn">Изпращане</button>
            </div>

        </div>

    <?php } else { ?>
        <div class="empty-message">
                Съобщенията ти ще се появяват тук
            </div>
    <?php } ?>
</div>