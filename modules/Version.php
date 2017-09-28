<?php

class Version extends BaseModule
{
    public function __construct()
    {
        parent::__construct();
    }

    public function entryPoint()
    {
        $list = Database::select("SELECT * FROM version ORDER BY id DESC LIMIT 1")[0];

        $data["version"] = $list["version"];
        $data["maintenance"] = MAINTENANCE;
        print json_encode($data, JSON_PRETTY_PRINT);
        return;
    }
}