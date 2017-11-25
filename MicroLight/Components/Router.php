<?php
abstract class Router
{
    public static $version = "1.0.0";
    private static function parser(): string{
        $first = explode("&", $_SERVER["QUERY_STRING"])[0];
        return !empty($first) ? $first:"main";
    }
    public static function get($page, $instanceOfClass){
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if(self::parser()==$page){
                $params = $_GET;
                foreach ($params as $key=>$value){
                    $params["action"] = $key;
                    unset($params[$key]);
                    break;
                }
                $instanceOfClass::get($params);
            }
        }
    }
    public static function post($page, $instanceOfClass){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if(self::parser()==$page){
                $instanceOfClass::post($input);
            }
        }
    }
    public static function delete($page, $instanceOfClass){
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            if(self::parser()==$page){
                $input = json_decode(file_get_contents('php://input'), true);
                $instanceOfClass::delete($input);
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
    public static function patch($page, $instanceOfClass){
        if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
            if(self::parser()==$page){
                $input = json_decode(file_get_contents('php://input'), true);
                $instanceOfClass::patch($input);
            }
        }
    }
}