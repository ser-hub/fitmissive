<?php

namespace Application\Models;

class Chat
{
    private $userA, $userB;
    private $userAPicture, $userBPicture;
    private $chatId;
    private $messages;
    private $unseenMessages;

    public function __construct($userA, $userB)
    {
        $this->userA = $userA;
        $this->userB = $userB;
        $this->messages = array();
    }

    public function getUserA()
    {
        return $this->userA;
    }

    public function getUserB()
    {
        return $this->userB;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function setUserA($userA)
    {
        $this->userA = $userA;
    }

    public function setUserB($userB)
    {
        $this->userB = $userB;
    }

    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    public function getUserAPicture()
    {
        return $this->userAPicture;
    }

    public function getUserBPicture()
    {
        return $this->userBPicture;
    }

    public function setUserAPicture($userAPicture)
    {
        $this->userAPicture = $userAPicture;
    }

    public function setUserBPicture($userBPicture)
    {
        $this->userBPicture = $userBPicture;
    }

    public function getChatId()
    {
        return $this->chatId;
    }

    public function setChatId($chatId)
    {
        $this->chatId = $chatId;
    }

    public function setUnseenMessages($unseenMessages)
    {
        $this->unseenMessages = $unseenMessages;
    }

    public function getUnseenMessages()
    {
        return $this->unseenMessages;
    }
}