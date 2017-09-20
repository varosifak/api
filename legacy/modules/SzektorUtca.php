<?php

class SzektorUtca extends BaseModule
{
    public function __construct()
    {
        parent::__construct();
    }

    public function entryPoint()
    {
        $list = Database::select("SELECT * FROM utcak ORDER BY utca_nev ASC");
        print json_encode($list, JSON_PRETTY_PRINT);
        return;
    }
}