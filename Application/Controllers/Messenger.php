<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Utilities\Time;

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

        if ($messages) {
            foreach ($messages as $message) {
                $message->sent_at = Time::elapsedString(date_create($message->sent_at));
            }
        }

        $this->view('messenger/messenger', [
            'sender' => $loggedUser->username,
            'userColor' => $loggedUser->color_hex,
            'receiver' => $username,
            'chats' => $chats,
            'messages' => $messages,
            'senderPic' => $this->userService->getPicturePathOf($loggedUser->username),
            'receiverPic' => $this->userService->getPicturePathOf($username)
        ]);
    }

    public function messages($target = false)
    {
        $messagesParsed = [];
        if ($target) {
            $receiver = $this->userService->getUser($target);

            if ($receiver) {
                $chatId = $this->chatService->startChat($this->loggedUser, $receiver->user_id);
                if (is_numeric($chatId)) {
                    $messages = $this->chatService->getMessagesOf($chatId);
                    if ($messages) {
                        foreach ($messages as $message) {
                            $messagesParsed[] = [
                                'author' => $message->user_id,
                                'timestamp' => str_replace(' ', 'T', $message->sent_at) . 'Z',
                                'text' => $message->message
                            ];
                        }
                        $this->chatService->setAllMessagesSeen($chatId, $receiver->user_id);
                    }
                }
            }
        }

        echo json_encode([
            'ownPicture' => $this->userService->getPicturePathOf($this->loggedUsername),
            'targetPicture' => $this->userService->getPicturePathOf($target),
            'messages' => $messagesParsed
        ]);
    }
}
