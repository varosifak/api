<?php
use MicroLight\Components\JSON as JSON;
abstract class Main extends BaseModule
{
    static public function any(){
        $info = array(
            'code' => 400,
            'message' => 'Bad request',
            'help' => 'To use API, please read the documentation.'
        );
        JSON::set("main", $info);
    }
}