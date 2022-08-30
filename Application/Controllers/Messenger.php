<?php

namespace Application\Controllers;

use Application\Core\Controller;

class Messenger extends Controller
{
    public function index($username = null)
    {
        $loggedUser =  $this->userService->getLoggedUser();
        $messages = [];

        if ($username) {
            $receiver = $this->userService->getUser($username);

            if (!$receiver) {
                $username = null;
            } else {
                $chatId = $this->chatService->startChat($loggedUser->user_id, $receiver->user_id);
                if (is_numeric($chatId)) {
                    $messages = $this->chatService->getMessagesOf($chatId);
                    $this->chatService->setAllMessagesSeen($chatId, $receiver->user_id);
                }
            }
        }

        $chats = $this->chatService->getChats($loggedUser->user_id);

        foreach ($chats as $chat) {
            if ($chat->getUserA() != $loggedUser->username) {
                $chat->setUserAPicture($this->userService->getPicturePathOf($chat->getUserA()));
            } else {
                $chat->setUserBPicture($this->userService->getPicturePathOf($chat->getUserB()));
            }
        }

        foreach ($chats as $chat) {
            if ($chat->getUnseenMessages()) {
                if ($chat->getUserA() != $loggedUser->username) {
                    $username = $chat->getUserA();
                } else {
                    $username = $chat->getUserB();
                }

                $receiver = $this->userService->getUser($username);
                $chatId = $this->chatService->startChat($loggedUser->user_id, $receiver->user_id);
                $messages = $this->chatService->getMessagesOf($chatId);
                $this->chatService->setAllMessagesSeen($chatId, $receiver->user_id);

                break;
            }
        }

        $this->view('messenger', array(
            'sender' => $loggedUser->username,
            'receiver' => $username,
            'chats' => $chats,
            'messages' => $messages,
            'senderPic' => $this->userService->getPicturePathOf($loggedUser->username),
            'receiverPic' => $this->userService->getPicturePathOf($username)
        ));
    }
}
