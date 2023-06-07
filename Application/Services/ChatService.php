<?php

namespace Application\Services;

use Application\Repositories\ChatRepository;
use Application\Repositories\UserRepository;
use Application\Utilities\Constants;

class ChatService
{
    private static $instance;
    private $chatRepository,
            $userRepository;
    private $userService;

    private function __construct()
    {
        $this->userRepository = UserRepository::getInstance();
        $this->chatRepository = ChatRepository::getInstance();
        $this->userService = UserService::getInstance();
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function startChat($userA, $userB)
    {
        return $this->chatRepository->startChat($userA, $userB);
    }

    public function getChats($userId = null)
    {
        $counter = 0;
        $chats = $this->chatRepository->getChats($userId, Constants::CHATS_MAX_COUNT);
        $username = $this->userRepository->find($userId)->username;

        if ($chats) {
            foreach ($chats as $chat) {
                $chat->setUserA($this->userRepository->find($chat->getUserA())->username);
                $chat->setUserBPicture($this->userService->getPicturePathOf($chat->getUserB()));
                $chat->setUserB($this->userRepository->find($chat->getUserB())->username);
                $chat->setUserBPicture($this->userService->getPicturePathOf($chat->getUserB()));
    
                $messages = $this->getMessagesOf($chat->getChatId());
    
                if ($messages) {
                    $counter = 0;
                    foreach ($messages as $message) {
                        if ($message->user_id != $username && $message->seen == NULL) {
                            $counter++;
                        }
                    }
                    $chat->setUnseenMessages($counter);
                }
            }
        } else {
            return [];
        }

        return $chats;
    }

    public function getMessagesOf($chatId)
    {
        $messages = $this->chatRepository->getMessages($chatId);

        if ($messages) {
            foreach ($messages as $message) {
                $message->user_id = $this->userRepository->find($message->user_id)->username;
            }
        } else {
            return [];
        }

        return $messages;
    }

    public function getMessagesJSONFormat($chatId)
    {
        $messagesParsed = [];
        $messages = $this->getMessagesOf($chatId);
        foreach ($messages as $message) {
            $messagesParsed[] = [
                'author' => $message->user_id,
                'timestamp' => str_replace(' ', 'T', $message->sent_at) . 'Z',
                'text' => $message->message
            ];
        }

        return $messagesParsed;
    }

    public function unseenMessagesCount($userId)
    {
        $chats = $this->getChats($userId);
        $username = $this->userRepository->find($userId)->username;

        $unseenMessages = 0;
        if ($chats) {
            foreach ($chats as $chat) {
                $messages = $this->getMessagesOf($chat->getChatId());

                if ($messages) {
                    foreach ($messages as $message) {
                        if ($message->user_id != $username && $message->seen == NULL) {
                            $unseenMessages++;
                        }
                    }
                }
            }
        }

        return $unseenMessages;
    }

    public function setAllMessagesSeen($chatId, $userId)
    {
        return $this->chatRepository->seenAllMessages($chatId, $userId);
    }
}
