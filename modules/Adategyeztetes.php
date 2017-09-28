<?php

class Adategyeztetes extends BaseModule
{
    public function __construct()
    {
        parent::__construct();
    }

    public function entryPoint()
    {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $token = $_GET["token"];
        $neptun = strtoupper(@$_POST["neptun"]);

        $sindb = Database::select("SELECT * FROM authentication WHERE auth_token=? AND neptun_kod=?", array($token, $neptun));
        if (count($sindb) == 0) {
            $data["code"] = 0;
            $data["message"] = "A hitelesítés nem sikerült, jelentkezzen be újra!";
        } else {
            $tdb = Database::$db;
            $bps = $tdb->prepare("UPDATE `users` SET nev = :nev, email = :email WHERE neptun=:neptun");
            $bps->bindParam(":nev", $name);
            $bps->bindParam(":email", $email);
            $bps->bindParam(":neptun", $neptun);
            $bps->execute();

            $data["code"] = 1;
            $data["message"] = "Köszönjük az adategyeztetést, jó munkát kívánunk!";
            $data["name"] = $name;
            $data["email"] = $email;
        }
        print json_encode($data, JSON_PRETTY_PRINT);
        return;
    }
}