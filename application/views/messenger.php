<!DOCTYPE html>
<html>

<head>
    <title>Fitmissive Messenger</title>
    <link rel="stylesheet" href="/css/common.css" type="text/css">
    <link rel="stylesheet" href="/css/messenger.css" type="text/css">
</head>

<body>
    <?php require_once 'Application/Views/Common/header.php' ?>

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
                    <img src="<?= $receiverPicture ?>" width="50px" height="50px" alt="Profile picture">
                    <div class="chat-button-text">
                        <div><?= $receiver ?></div>
                        <div></div>
                    </div>
                </div>
            </a>
        <?php } ?>
    </div>

    <?php if (isset($data['receiver'])) { ?>
        <div class="form">
            <div class="messages">
                <?php if (!empty($data['messages'])) { ?>
                    <?php $currentAuthor = null ?>
                    <?php foreach ($data['messages'] as $message) { ?>
                        <?php if ($message->user_id !== $currentAuthor) { ?>
                            <?php if ($currentAuthor != null) { ?>
            </div>
        </div>
    <?php } ?>
    <div class="chat-item">
        <div style="margin-right: 10px;">
            <img src="<?php
                            if ($message->user_id == $data['sender']) {
                                echo $data['senderPic'];
                            } else {
                                echo $data['receiverPic'];
                            }
                        ?>" height="30" width="30" alt="profile picture">
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
<?php } ?>
</div>
<input type="hidden" class="sender" value="<?= $data['senderPic'] ?>">
<input type="hidden" class="receiver" value="<?= $data['receiverPic'] ?>">
<input type="hidden" class="username" value="<?= $data['sender'] ?>">
<div class="status"></div>
<textarea class="input" name="input"></textarea>
</div>

<?php include "Application/Socket/chat.php" ?>

</script>
<?php } ?>

<?php require_once 'Application/Views/Common/footer.php' ?>
</body>

</html>