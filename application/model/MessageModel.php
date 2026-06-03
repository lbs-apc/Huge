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
        // Join with users to get sender names
        $sql = "SELECT messages.*, users.user_name as sender_name 
                FROM messages 
                JOIN users ON messages.sender_id = users.user_id
                WHERE (messages.sender_id = :me AND messages.receiver_id = :other) 
                   OR (messages.sender_id = :other AND messages.receiver_id = :me) 
                ORDER BY messages.created_at ASC";
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

    public static function createGroup($group_name, $user_ids)
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        $my_id = Session::get('user_id');

        // Create the group
        $sql = "INSERT INTO groups (name, creator_id, created_at) VALUES (:name, :creator, :now)";
        $q = $db->prepare($sql);
        $q->execute(array(
            ':name' => $group_name,
            ':creator' => $my_id,
            ':now' => date('Y-m-d H:i:s')
        ));
        $group_id = $db->lastInsertId();

        // Add creator to group
        $sql_add = "INSERT INTO group_users (group_id, user_id) VALUES (:gid, :uid)";
        $q_add = $db->prepare($sql_add);
        $q_add->execute(array(':gid' => $group_id, ':uid' => $my_id));

        // Add other members
        if (!empty($user_ids)) {
            foreach ($user_ids as $user_id) {
                if ($user_id != $my_id) {
                    $q_add->execute(array(':gid' => $group_id, ':uid' => $user_id));
                }
            }
        }

        return $group_id;
    }

    public static function getGroupsForUser($user_id)
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT groups.* FROM groups 
                JOIN group_users ON groups.id = group_users.group_id 
                WHERE group_users.user_id = :uid";
        $q = $db->prepare($sql);
        $q->execute(array(':uid' => $user_id));
        return $q->fetchAll();
    }

    public static function getGroup($group_id)
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT * FROM groups WHERE id = :id LIMIT 1";
        $q = $db->prepare($sql);
        $q->execute(array(':id' => $group_id));
        return $q->fetch();
    }

    public static function sendGroupMessage($group_id, $message_text)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            $my_id = Session::get('user_id');
            $date_now = date('Y-m-d H:i:s');

            // For group messages, we don't have a 1:1 chat_id or a single receiver_id.
            // We set them to NULL (requires the ALTER TABLE from the installation script).
            $sql = "INSERT INTO messages (chat_id, sender_id, receiver_id, group_id, content, is_read, created_at) 
                    VALUES (NULL, :sender_id, NULL, :group_id, :content, 0, :created_at)";
            
            $query = $database->prepare($sql);
            $query->execute(array(
                ':sender_id' => $my_id,
                ':group_id' => $group_id,
                ':content' => $message_text,
                ':created_at' => $date_now
            ));

            return true;
        } catch (PDOException $e) {
            Session::add('feedback_negative', "Group Message Error: " . $e->getMessage());
            return false;
        }
    }

    public static function getGroupMessages($group_id)
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        // Join with users to get sender names
        $sql = "SELECT messages.*, users.user_name as sender_name 
                FROM messages 
                JOIN users ON messages.sender_id = users.user_id 
                WHERE messages.group_id = :gid 
                ORDER BY messages.created_at ASC";
        $q = $db->prepare($sql);
        $q->execute(array(':gid' => $group_id));
        return $q->fetchAll();
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

    public static function getUnreadGroupCount($user_id)
    {
        // Simple unread count for groups - for now just count all where sender is not me
        // In a real app, you'd need a separate table to track what each user has read in a group
        return 0; 
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
