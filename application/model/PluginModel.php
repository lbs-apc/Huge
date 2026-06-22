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

    public static function installPlugin()
    {
        return false;
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

        $sql = "SELECT active FROM plugins WHERE plugin_id = :plugin_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':plugin_id' => $plugin_id));
        $plugin = $query->fetch();

        if (!$plugin) {
            return false;
        }

        $newActive = ($plugin->active == 1) ? 0 : 1;

        $sql = "UPDATE plugins SET active = :active WHERE plugin_id = :plugin_id LIMIT 1";
        $query = $database->prepare($sql);
        $query->execute(array(':active' => $newActive, ':plugin_id' => $plugin_id));

        return ($query->rowCount() == 1);
    }

}
