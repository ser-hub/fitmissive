<?php

namespace Application\Services;

use Application\Repositories\ChatRepository;
use Application\Repositories\UserRepository;
use Application\Utilities\Constants;

class ChatService 
{
    private static $instance;
    private $chatRepository;
    private $userRepository;
    
    private function __construct()
    {
        $this->userRepository = UserRepository::getInstance();
        $this->chatRepository = ChatRepository::getInstance();
    }
    
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    public function startChat($userA, $userB) {
        return $this->chatRepository->startChat($userA, $userB);
    }

    public function getChats($userId = null) {
        $chats = $this->chatRepository->getChats($userId, Constants::CHATS_MAX_COUNT);
        $username = $this->userRepository->find($userId)->username;

        if (!empty($chats)) {

            foreach ($chats as $chat) {
                $chat->setUserA($this->userRepository->find($chat->getUserA())->username);
                $chat->setUserB($this->userRepository->find($chat->getUserB())->username);

                $messages = $this->getMessagesOf($chat->getChatId());

                if ($messages) {
                    foreach ($messages as $message) {
                        if ($message->user_id != $username && $message->seen == NULL) {
                            $chat->setUnseenMessages(true);
                        }
                    }
                }
            }

            return $chats;
        } else {
            return false;
        }
    }

    public function getMessagesOf($chatId) {
        $messages = $this->chatRepository->getMessages($chatId);

        if ($messages) {
            foreach ($messages as $message) {
                $message->user_id = $this->userRepository->find($message->user_id)->username;
            }
        }

        return $messages;
    }

    public function unseenMessages($userId) {
        $chats = $this->getChats($userId);
        $username = $this->userRepository->find($userId)->username;

        foreach($chats as $chat) {
            $messages = $this->getMessagesOf($chat->getChatId());

            if ($messages) {
                foreach ($messages as $message) {
                    if ($message->user_id != $username && $message->seen == NULL) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function setAllMessagesSeen($chatId, $userId)
    {
        return $this->chatRepository->seenAllMessages($chatId, $userId);
    }
}