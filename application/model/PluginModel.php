<?php

class PluginModel
{
    public static function getAllPlugins()
    {
        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT * FROM plugins ORDER BY created_at DESC";
        $query = $database->prepare($sql);
        $query->execute();

        return $query->fetchAll();
    }

    public static function displayPlugins()
    {
        return self::getAllPlugins();
    }

    public static function installPlugin($plugin_id)
    {
        if (!$plugin_id) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT installed FROM plugins WHERE plugin_id = :id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':id' => $plugin_id));
        $plugin = $query->fetch();

        if (!$plugin) {
            return false;
        }

        $newInstalls = ((int)$plugin->installed) + 1;

        $sql = "UPDATE plugins SET installed = :installed, active = 1 WHERE plugin_id = :id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':installed' => $newInstalls, ':id' => $plugin_id));

        return ($query->rowCount() == 1);
    }

    public static function activePlugins()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        $sql = "SELECT * FROM plugins WHERE active = 1 ORDER BY created_at DESC";
        $query = $database->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public static function togglePlugin($plugin_id)
    {
        if (!$plugin_id) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT active, installed FROM plugins WHERE plugin_id = :plugin_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':plugin_id' => $plugin_id));
        $plugin = $query->fetch();

        if (!$plugin) {
            return false;
        }

        if ((int)$plugin->installed <= 0 && (int)$plugin->active == 0) {
            return false;
        }

        $newActive = ((int)$plugin->active == 1) ? 0 : 1;

        $sql = "UPDATE plugins SET active = :active WHERE plugin_id = :plugin_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':active' => $newActive, ':plugin_id' => $plugin_id));

        return ($query->rowCount() == 1);
    }

    public static function uninstallPlugin($plugin_id)
    {
        if (!$plugin_id) {
            return false;
        }

        $database = DatabaseFactory::getFactory()->getConnection();

        $sql = "SELECT active FROM plugins WHERE plugin_id = :plugin_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':plugin_id' => $plugin_id));
        $plugin = $query->fetch();

        if (!$plugin || (int)$plugin->active == 0) {
            return false;
        }

        $sql = "UPDATE plugins SET active = 0 WHERE plugin_id = :plugin_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':plugin_id' => $plugin_id));

        return ($query->rowCount() == 1);
    }

}
