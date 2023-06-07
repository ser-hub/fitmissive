<?php 

namespace Application\Repositories;

use Application\Database\DB;
use Application\Models\Chat;

class ChatRepository
{
    private static $instance = null;
    private $db;
    private const CHATS_TABLE = "chats";
    private const MESSAGES_TABLE = "messages";

    private function __construct()
    {
        $this->db = DB::getInstance();
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function startChat($userA, $userB) {
        $queryString = "SELECT * FROM " . self::CHATS_TABLE . " WHERE user_a = ? AND user_b = ?";
        if ($this->db->query($queryString, [$userA, $userB])->count()){
            $chatId = $this->db->query($queryString, [$userA, $userB])->first()->chat_id;
        } elseif ($this->db->query($queryString, [$userB, $userA])->count()) {
            $chatId = $this->db->query($queryString, [$userB, $userA])->first()->chat_id;
        } else {
            $this->db->insert(self::CHATS_TABLE, [
                'user_a' => $userA,
                'user_b' => $userB,
                'started_at' => date('Y-m-d H:i:s')
            ]);
            $chatId = $this->db->query($queryString, [$userA, $userB])->first()->chat_id;
        }

        return $chatId;
    }

    public function getChats($userId = null, $top = 0) {
        $queryString = "SELECT * FROM " . self::CHATS_TABLE . " WHERE user_a = ? OR user_b = ? ORDER BY started_at DESC";
        if ($top > 0) {
            $queryString .= " LIMIT " . $top;
        }
        $chats = $this->db->query($queryString, [$userId, $userId]);

        $chatsArray = array();

        if ($chats->results()) {
            $chatsResults = $chats->results();
            
            foreach ($chatsResults as $chat) {
                $chatModel = new Chat($chat->user_a, $chat->user_b);
                $chatModel->setChatId($chat->chat_id);
                $chatsArray[] = $chatModel;
            }
        }

        if (!empty($chatsArray)) {
            return $chatsArray;
        } else {
            return false;
        }
    }

    public function getMessages($chatId) {
        $messages = $this->db->get(self::MESSAGES_TABLE, array(
            'chat_id',
            '=',
            $chatId
        ));

        if ($messages->results()) {
            return $messages->results();
        } else {
            return false;
        }
    }

    public function seenAllMessages($chatId, $userId)
    {
        return $this->db->query(
            'UPDATE ' . self::MESSAGES_TABLE . ' SET seen = ? WHERE chat_id = ? AND user_id = ? AND seen is NULL',
            [
                date('Y-m-d H:i:s'),
                $chatId,
                $userId
            ]);
    }

    public function deleteChatsOf($userId)
    {
        if (!$this->db->delete(self::CHATS_TABLE, array('user_a', '=', $userId)) || 
            !$this->db->delete(self::CHATS_TABLE, array('user_b', '=', $userId))) {
            return false;
        } else {
            $this->db->delete(self::MESSAGES_TABLE, array('user_id', '=', $userId));
            return true;
        }

    }
}