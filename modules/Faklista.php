<?php

class Faklista extends BaseModule
{
    public function __construct()
    {
        parent::__construct();
    }

    public function entryPoint()
    {
        $dataset = array();
        $list = Database::select("SELECT kategoria FROM fa_kategoriak ORDER BY id ASC");
        for ($i = 0; $i < count($list); $i++) {
            $dataset[$i]["kategoria"] = $list[$i]["kategoria"];
            $dataset[$i]["fajok"] = Database::select("SELECT * FROM fa_fajok WHERE kategoria=? ORDER BY magyar ASC", array($dataset[$i]["kategoria"]));
            for ($i2 = 0; $i2 < count($dataset[$i]["fajok"]); $i2++) {
                $dataset[$i]["fajok"][$i2]["kepek"] = json_decode(@$dataset[$i]["fajok"][$i2]["kepek"]);
            }
        }
        print json_encode($dataset, JSON_PRETTY_PRINT);
        return;
    }
}