<?php
/**
 * Created by PhpStorm.
 * User: xavvi
 * Date: 2017. 10. 29.
 * Time: 21:23
 */

namespace MicroLight\Components;
abstract class Router
{
    public static $version = "0.0.1";
    private static function parser(): string{
        $first = explode("&", $_SERVER["QUERY_STRING"])[0];
        return !empty($first) ? $first:"main";
    }
    public static function any($page, $instanceOfClass){
        $instanceOfClass::any();
    }
    public static function get($page, $instanceOfClass){
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if(self::parser()==$page){
                $instanceOfClass::get();
            }
        }
    }
    public static function post($page, $instanceOfClass){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(self::parser()==$page){
                $instanceOfClass::post();
            }
        }
    }
    public static function delete($page, $instanceOfClass){
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            if(self::parser()==$page){
                $instanceOfClass::delete();
            }
        }
    }
    public static function put($page, $instanceOfClass){
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            if(self::parser()==$page){
                $instanceOfClass::put();
            }
        }
    }
}