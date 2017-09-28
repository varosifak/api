<?php

class CheckIn extends BaseModule
{
    public function __construct()
    {
        parent::__construct();
    }

    public function entryPoint()
    {
        $auth_token = @$_POST["auth_token"];
        $neptun = strtoupper(@$_POST["neptun"]);
        $szektorutca = explode("/", @$_POST["szektorutca"]);
        $felev = @$_POST["felev"];
        if (empty($neptun)) {
            $data["code"] = 0;
            $data["modal"]["title"] = "Automatikus kijelentkeztetés...";
            $data["modal"]["text"] = "Hibásan tárolt authentikációs adatokat találtunk, ezért a fiókod biztonsága érdekében kijelentkeztetünk. Jelentkezz be újra!";
            print json_encode($data, JSON_PRETTY_PRINT);
            return;
        }
        if (empty(@$szektorutca[0]) || empty(@$szektorutca[1])) {
            $data["code"] = 0;
            $data["modal"]["title"] = "Automatikus kijelentkeztetés...";
            $data["modal"]["text"] = "Hibásan tárolt authentikációs adatokat találtunk, ezért a fiókod biztonsága érdekében kijelentkeztetünk. Jelentkezz be újra!";
            print json_encode($data, JSON_PRETTY_PRINT);
            return;
        }
        if (empty($felev)) {
            $data["code"] = 0;
            $data["modal"]["title"] = "Automatikus kijelentkeztetés...";
            $data["modal"]["text"] = "Hibásan tárolt authentikációs adatokat találtunk, ezért a fiókod biztonsága érdekében kijelentkeztetünk. Jelentkezz be újra!";
            print json_encode($data, JSON_PRETTY_PRINT);
            return;
        }

        $adata = array($auth_token, $neptun, $szektorutca[0], $szektorutca[1], $felev);
        $atokencheck = Database::select("SELECT * FROM authentication WHERE auth_token=? AND neptun_kod=? AND szektor_id=? AND utca_id=? AND felev=?", $adata);
        if (count($atokencheck) == 0) {
            $data["code"] = 0;
            $data["modal"]["title"] = "Automatikus kijelentkeztetés...";
            $data["modal"]["text"] = "A munkamenet nem található, ezért kijelentkeztetünk. Jelentkezz be újra!";
            print json_encode($data, JSON_PRETTY_PRINT);
            return;
        } else {
            $udata = Database::select("SELECT * FROM users WHERE UPPER(neptun)=UPPER(?)", array($neptun))[0];
            $felevStatus = Database::select("SELECT active, informaciok FROM felevek WHERE felev=?", array($felev))[0];
            $data["felevStatus"] = $felevStatus["active"] == 1 ? true : false;
            $data["felevInformaciok"] = str_replace("\n", "<br>", $felevStatus["informaciok"]);

            $data["neptun"] = $neptun;
            $data["szektor"] = $szektorutca[0];
            $data["utca"] = $szektorutca[1];
            $data["felev"] = $felev;
            $data["auth_token"] = $auth_token;
            $data["name"] = $udata["nev"];
            $data["email"] = $udata["email"];
            if (empty($udata["nev"]) || empty($udata["email"])) {
                $data["code"] = 2;
                $data["modal"]["title"] = "Kedves Hallgató!";
                $data["modal"]["text"] = "Az alkalmazás használatához további adategyeztetés szükséges, mivel a rendszerben hiányos adataink vannak Önről!";
                print json_encode($data, JSON_PRETTY_PRINT);
            } else {
                $data["code"] = 1;
                print json_encode($data, JSON_PRETTY_PRINT);
            }
        }
        return;
    }
}