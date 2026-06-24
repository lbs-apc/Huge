<?php

class MessageModel
{
    public static function getAllMessages()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "CALL sp_getAllMessages(:user_id)";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => Session::get('user_id')));
        return $query->fetchAll();
    }

    public static function getConversationWith($other_user_id)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "CALL sp_getConversation(:me, :other)";
        $query = $database->prepare($sql);
        $query->execute(array(':me' => Session::get('user_id'), ':other' => $other_user_id));
        return $query->fetchAll();
    }

    public static function sendMessage($receiver_id, $message_text)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "CALL sp_sendMessage(:sender_id, :receiver_id, :content)";
        $query = $database->prepare($sql);
        $query->execute(array(
            ':sender_id' => Session::get('user_id'),
            ':receiver_id' => $receiver_id,
            ':content' => $message_text
        ));
        return true;
    }

    public static function createGroup($group_name, $user_ids)
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        $my_id = Session::get('user_id');

        // Call SP to create group and get ID
        $sql = "CALL sp_createGroup(:name, :creator, @group_id)";
        $q = $db->prepare($sql);
        $q->execute(array(':name' => $group_name, ':creator' => $my_id));

        $res = $db->query("SELECT @group_id as id")->fetch();
        $group_id = $res->id;

        // Add creator
        self::addUserToGroup($group_id, $my_id);

        // Add other members
        if (!empty($user_ids)) {
            foreach ($user_ids as $user_id) {
                if ($user_id != $my_id) {
                    self::addUserToGroup($group_id, $user_id);
                }
            }
        }

        return $group_id;
    }

    private static function addUserToGroup($group_id, $user_id)
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        $sql = "CALL sp_addUserToGroup(:gid, :uid)";
        $q = $db->prepare($sql);
        $q->execute(array(':gid' => $group_id, ':uid' => $user_id));
    }

    public static function getGroupsForUser($user_id)
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        $sql = "CALL sp_getGroupsForUser(:uid)";
        $q = $db->prepare($sql);
        $q->execute(array(':uid' => $user_id));
        return $q->fetchAll();
    }

    public static function getGroup($group_id)
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        $sql = "CALL sp_getGroup(:id)";
        $q = $db->prepare($sql);
        $q->execute(array(':id' => $group_id));
        return $q->fetch();
    }

    public static function sendGroupMessage($group_id, $message_text)
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "CALL sp_sendGroupMessage(:sender_id, :group_id, :content)";
        $query = $database->prepare($sql);
        return $query->execute(array(
            ':sender_id' => Session::get('user_id'),
            ':group_id' => $group_id,
            ':content' => $message_text
        ));
    }

    public static function getGroupMessages($group_id)
    {
        $db = DatabaseFactory::getFactory()->getConnection();
        $sql = "CALL sp_getGroupMessages(:gid)";
        $q = $db->prepare($sql);
        $q->execute(array(':gid' => $group_id));
        return $q->fetchAll();
    }

    public static function getUnreadCount($user_id)
    {
        if (!$user_id) return 0;
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "CALL sp_getUnreadCount(:user_id)";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id));
        $res = $query->fetch();
        return isset($res->c) ? (int)$res->c : 0;
    }

    public static function getUnreadGroupCount($user_id)
    {
        return 0;
    }

    public static function markMessagesAsReadFrom($from_user_id, $to_user_id)
    {
        if (!$from_user_id || !$to_user_id) return false;
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "CALL sp_markMessagesAsRead(:from, :to)";
        $query = $database->prepare($sql);
        $query->execute(array(':from' => $from_user_id, ':to' => $to_user_id));
        return true;
    }
}
