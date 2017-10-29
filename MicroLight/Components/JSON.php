<?php
namespace MicroLight\Components;
class JSON
{
    public static $dataSet = array();
    public static $version = "1.0.0";
    public static function get() : array {
        return JSON::$dataSet;
    }
    public static function set($namespace, $array) {
        if(isset(JSON::$dataSet[$namespace])) {
            JSON::$dataSet[$namespace] = array_merge(JSON::$dataSet, $array);
        }else{
            JSON::$dataSet[$namespace] = $array;
        }
    }
}