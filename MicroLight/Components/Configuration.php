<?php
namespace MicroLight\Components;
class Configuration
{
    public static $version = "1.0.0";
    public static function loadConfig($configurationFilePatch){
        $configData = array('version' => '1.0.0');
        if(!is_file($configurationFilePatch)){
            $configData = array_merge(
                $configData,
                array('code' => 404, 'message' => 'Configuration file is not exists!')
            );
        }else{
            require_once $configurationFilePatch;
            $configData = array_merge(
                $configData,
                array('code' => 200)
            );
        }
        JSON::set('config', $configData);
    }

    public static function serializeRequests(&$what){
        foreach ($what AS $key => $value)
            $what[$key] = @addslashes(@htmlspecialchars($value));
    }
}