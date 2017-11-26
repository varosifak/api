<?php

abstract class BaseModule
{
    public static $version = "1.0.1";

    static function expectedParameters($where, $listOfParameters): array
    {
        $enabled = $where == NULL && count($listOfParameters) > 0 ? false : true;
        $listofnotfound = array();
        foreach ($listOfParameters as $parameter) {
            if (!@array_key_exists($parameter, $where)) {
                array_push($listofnotfound, $parameter);
                $enabled = false;
            }
        }
        return [$enabled, $listofnotfound];
    }

    abstract static public function get($params);

    abstract static public function post($params);

    abstract static public function delete($params);

    abstract static public function put();

    abstract static public function patch($params);

    abstract static public function propfind($params);
}