<?php
class MessageModel
{
    public static function getAllMessages()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT * FROM messages WHERE sender_id = :user_id OR receiver_id = :user_id ORDER BY created_at DESC";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => Session::get('user_id')));
        return $query->fetchAll();
    }

    public static function getConversationWith($other_user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT * FROM messages WHERE (sender_id = :me AND receiver_id = :other) OR (sender_id = :other AND receiver_id = :me) ORDER BY created_at ASC";
        $query = $database->prepare($sql);
        $query->execute(array(':me' => Session::get('user_id'), ':other' => $other_user_id));
        return $query->fetchAll();
    }

    public static function sendMessage($receiver_id, $message_text)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $my_id = Session::get('user_id');

        $query_check = $database->prepare("SELECT * FROM chats WHERE (sender_id = :user1 AND receiver_id = :user2) OR (sender_id = :user2 AND receiver_id = :user1) LIMIT 1");
        $query_check->execute(array(':user1' => $my_id, ':user2' => $receiver_id));
        $chat_row = $query_check->fetch();

        if ($chat_row) {
            $chat_id = $chat_row->chat_id;
        } else {
            $query_new_chat = $database->prepare("INSERT INTO chats (sender_id, receiver_id) VALUES (:user1, :user2)");
            $query_new_chat->execute(array(':user1' => $my_id, ':user2' => $receiver_id));
            $chat_id = $database->lastInsertId();
        }


        $date_now = date('Y-m-d H:i:s');
        $query_message = $database->prepare("INSERT INTO messages (chat_id, sender_id, receiver_id, content, is_read, created_at) VALUES (:chat_id, :sender_id, :receiver_id, :content, 0, :created_at)");

        $query_message->execute(array(
            ':chat_id' => $chat_id,
            ':sender_id' => $my_id,
            ':receiver_id' => $receiver_id,
            ':content' => $message_text,
            ':created_at' => $date_now
        ));

        return true;
    }

    public static function getUnreadCount($user_id)
    {
        if (!$user_id) return 0;
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT COUNT(*) as c FROM messages WHERE receiver_id = :user_id AND is_read = 0";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id));
        $res = $query->fetch();
        return isset($res->c) ? (int)$res->c : 0;
    }

    public static function markMessagesAsReadFrom($from_user_id, $to_user_id)
    {
        if (!$from_user_id || !$to_user_id) return false;
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "UPDATE messages SET is_read = 1 WHERE sender_id = :from AND receiver_id = :to AND is_read = 0";
        $query = $database->prepare($sql);
        $query->execute(array(':from' => $from_user_id, ':to' => $to_user_id));
        return true;
    }
}
