<?php
class JSON
{
    public static $dataSet = array();
    public static $version = "1.0.1";

    public static function get(): array
    {
        return JSON::$dataSet;
    }

    public static function set($namespace, $array, $version=NULL)
    {
        if (!empty(JSON::$dataSet[$namespace])) {
            JSON::$dataSet[$namespace] = array_merge(JSON::$dataSet, $array);
        } else {
            JSON::$dataSet[$namespace] = $array;
        }
        if(!empty($version)){
            JSON::$dataSet[$namespace] = array_merge(JSON::$dataSet[$namespace], array("version" => $version));
        }
    }
}