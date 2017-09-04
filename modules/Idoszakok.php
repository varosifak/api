<?php

class Idoszakok extends BaseModule
{
    public function __construct()
    {
        parent::__construct();
    }

    public function entryPoint()
    {
        $list = Database::select("SELECT * FROM felevek ORDER BY active DESC, felev DESC");
        print json_encode($list, JSON_PRETTY_PRINT);
        return;
    }
}