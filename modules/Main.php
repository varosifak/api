<?php

abstract class Main extends BaseModule
{
    public static $version = "1.0.1";

    static public function get($params)
    {
        $info = array(
            'code' => 400,
            'message' => 'Bad request (GET)',
            'help' => 'To use API, please read the documentation.'
        );
        if (DEBUG) JSON::set("debug", array($params));
        JSON::set(get_class(), $info);
    }
}