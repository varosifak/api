<?php
class Configuration
{
    public static $version = "1.0.1";

    public static function loadConfig($configurationFilePatch)
    {
        if (!is_file($configurationFilePatch)) {
            $configData = array('code' => 404, 'message' => 'Configuration file is not exists!');
        } else {
            require_once $configurationFilePatch;
            $configData = array('code' => 200);
        }
        JSON::set('config', $configData, self::$version);
    }

    public static function serializeRequests(&$what)
    {
        foreach ($what AS $key => $value)
            $what[$key] = @addslashes(@htmlspecialchars($value));
    }
}