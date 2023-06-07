<?php

namespace Application\Controllers;

use Application\Core\Controller;

class Messenger extends Controller
{
    public function index($username = null)
    {
        $receiver = false;
        $loggedUser =  $this->userService->getLoggedUser();
        if ($username) {
            if ($this->userService->getUser($username)) {
                $receiver = $this->userService->getUser($username);
            }
        }

        $chats = $this->chatService->getChats($loggedUser->user_id);

        // if receiver is not set, go to first chat with unseen messages
        if (!$receiver) {
            foreach ($chats as $chat) {
                if ($chat->getUnseenMessages()) {
                    if ($chat->getUserA() != $loggedUser->username) {
                        $username = $chat->getUserA();
                    } else {
                        $username = $chat->getUserB();
                    }

                    $receiver = $this->userService->getUser($username);
                    $chatId = $this->chatService->startChat($loggedUser->user_id, $receiver->user_id);
                    $this->chatService->setAllMessagesSeen($chatId, $receiver->user_id);

                    break;
                }
            }
        }

        if (!$receiver && !empty($chats)) {
            if ($chats[0]->getUserA() != $loggedUser->username) {
                $this->index($chats[0]->getUserA());
            } else {
                $this->index($chats[0]->getUserB());
            }
            return;
        }

        // rework all logic here so that whenever a chat is started all messages are set to seen
        $selectedUserPicture = $selectedUserColor = '';
        if ($receiver) {
            $selectedUserPicture = $this->userService->getPicturePathOf($receiver->username);
            $selectedUserColor = $this->userService->getUserColor($receiver->username);

            $chatId = $this->chatService->startChat($loggedUser->user_id, $receiver->user_id);
            $this->chatService->setAllMessagesSeen($chatId, $receiver->user_id);
        }

        $this->view('messenger/messenger', [
            'sender' => $loggedUser->username,
            'userColor' => $loggedUser->color_hex,
            'receiver' => $username,
            'chats' => $chats,
            'selectedUserPicture' => $selectedUserPicture,
            'selectedUserColor' => $selectedUserColor
        ]);
    }
}
