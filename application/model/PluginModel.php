<?php

class PluginModel
{
    public static function getAllPlugins($user_id = null)
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        if ($user_id) {
            $sql = "SELECT p.*,
                           p.installed as total_installed,
                           COALESCE(up.installed, 0) as user_installed,
                           COALESCE(up.active, 0) as user_active
                    FROM plugins p
                    LEFT JOIN user_plugins up ON p.plugin_id = up.plugin_id AND up.user_id = :user_id
                    ORDER BY p.created_at DESC";
            $query = $database->prepare($sql);
            $query->execute(array(':user_id' => $user_id));
        } else {
            $sql = "SELECT * FROM plugins ORDER BY created_at DESC";
            $query = $database->prepare($sql);
            $query->execute();
        }

        return $query->fetchAll();
    }

    public static function displayPlugins($user_id = null)
    {
        return self::getAllPlugins($user_id);
    }

    public static function installPlugin($user_id, $plugin_id)
    {
        if (!$user_id || !$plugin_id) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT user_plugins_id, installed FROM user_plugins WHERE user_id = :user_id AND plugin_id = :plugin_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id, ':plugin_id' => $plugin_id));
        $userPlugin = $query->fetch();

        $success = false;
        if ($userPlugin) {
            $newInstalls = ((int)$userPlugin->installed) + 1;
            $sql = "UPDATE user_plugins SET installed = :installed, active = 1 WHERE user_id = :user_id AND plugin_id = :plugin_id LIMIT 1";
            $query = $database->prepare($sql);
            $query->execute(array(':installed' => $newInstalls, ':user_id' => $user_id, ':plugin_id' => $plugin_id));
            $success = ($query->rowCount() == 1);
        } else {
            $sql = "INSERT INTO user_plugins (user_id, plugin_id, installed, active, installed_at) VALUES (:user_id, :plugin_id, 1, 1, NOW())";
            $query = $database->prepare($sql);
            $query->execute(array(':user_id' => $user_id, ':plugin_id' => $plugin_id));
            $success = ($query->rowCount() == 1);
        }

        if ($success) {
            $sql = "UPDATE plugins SET installed = installed + 1 WHERE plugin_id = :plugin_id LIMIT 1";
            $query = $database->prepare($sql);
            $query->execute(array(':plugin_id' => $plugin_id));
        }

        return $success;
    }

    public static function activePlugins($user_id)
    {
        if (!$user_id) {
            return array();
        }

        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT p.* FROM plugins p
                INNER JOIN user_plugins up ON p.plugin_id = up.plugin_id
                WHERE up.user_id = :user_id AND up.active = 1
                ORDER BY p.created_at DESC";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id));
        return $query->fetchAll();
    }

    public static function togglePlugin($user_id, $plugin_id)
    {
        if (!$user_id || !$plugin_id) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT active, installed FROM user_plugins WHERE user_id = :user_id AND plugin_id = :plugin_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id, ':plugin_id' => $plugin_id));
        $userPlugin = $query->fetch();

        if (!$userPlugin || (int)$userPlugin->installed <= 0) {
            return false;
        }

        $newActive = ((int)$userPlugin->active == 1) ? 0 : 1;

        $sql = "UPDATE user_plugins SET active = :active WHERE user_id = :user_id AND plugin_id = :plugin_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':active' => $newActive, ':user_id' => $user_id, ':plugin_id' => $plugin_id));

        return ($query->rowCount() == 1);
    }

    public static function uninstallPlugin($user_id, $plugin_id)
    {
        if (!$user_id || !$plugin_id) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "DELETE FROM user_plugins WHERE user_id = :user_id AND plugin_id = :plugin_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':user_id' => $user_id, ':plugin_id' => $plugin_id));

        return true;
    }

}
