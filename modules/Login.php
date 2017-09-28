<?php

class Login extends BaseModule
{
    public function __construct()
    {
        parent::__construct();
    }

    public function entryPoint()
    {
        if (isset($_GET["action2"]) && $_GET["action2"] == "logout") {
            $auth = @$_GET["auth"];
            $neptun = @$_GET["neptun"];
            $felev = @$_GET["felev"];
            $tdb = Database::$db;
            $bps = $tdb->prepare("DELETE FROM authentication WHERE auth_token = :auth AND neptun_kod = :neptun AND felev = :felev");
            $bps->bindParam(":auth", $auth);
            $bps->bindParam(":neptun", $neptun);
            $bps->bindParam(":felev", $felev);
            $bps->execute();
            $data["code"] = 1;
            print json_encode($data, JSON_PRETTY_PRINT);
            return;
        }
        $neptun = strtoupper(@$_POST["neptun"]);
        $szektorutca = explode("/", @$_POST["szektorutca"]);
        $felev = @$_POST["felev"];
        if (empty($neptun)) {
            $data["code"] = 0;
            $data["modal"]["title"] = "Hiányzó adat";
            $data["modal"]["text"] = "A Neptun kód megadása kötelező! Kérlek pótold a hiányosságokat!";
            print json_encode($data, JSON_PRETTY_PRINT);
            return;
        }
        if (empty(@$szektorutca[0]) || empty(@$szektorutca[1])) {
            $data["code"] = 0;
            $data["modal"]["title"] = "Hiányzó adat";
            $data["modal"]["text"] = "Az utca név megadása kötelező! Kérlek pótold a hiányosságokat!";
            print json_encode($data, JSON_PRETTY_PRINT);
            return;
        }
        if (empty($felev)) {
            $data["code"] = 0;
            $data["modal"]["title"] = "Hiányzó adat";
            $data["modal"]["text"] = "A kezelni kívánt félév megadása kötelező! Kérlek pótold a hiányosságokat!";
            print json_encode($data, JSON_PRETTY_PRINT);
            return;
        }

        $kdata = array($neptun, $szektorutca[0], $szektorutca[1], $felev);
        $kapcsolat = Database::select("SELECT * FROM users_utcak WHERE UPPER(neptun_kod)=UPPER(?) AND szektor_id=? AND utca_id=? AND felev=?", $kdata);
        if (count($kapcsolat) == 0) {
            $data["code"] = 0;
            $data["modal"]["title"] = "Sikertelen belépés!";
            $data["modal"]["text"] = "A belépés nem sikerült, mert ezekkel az adatokkal (félév, utca, neptun kód) nincs bejegyezve az alkalmazás használatára. Amennyiben biztos, hogy helyesek az adatok, vegye fel a kapcsolatot a Kurzus oktatóival.";
            print json_encode($data, JSON_PRETTY_PRINT);
        } else {
            $udata = Database::select("SELECT * FROM users WHERE UPPER(neptun)=UPPER(?)", array($neptun))[0];

            $cat = time();

            $felevStatus = Database::select("SELECT active, informaciok FROM felevek WHERE felev=?", array($felev))[0];
            $data["felevStatus"] = $felevStatus["active"] == 1 ? true : false;
            $data["felevInformaciok"] = str_replace("\n", "<br>", $felevStatus["informaciok"]);

            $data["neptun"] = $neptun;
            $data["szektor"] = $szektorutca[0];
            $data["utca"] = $szektorutca[1];
            $data["felev"] = $felev;

            $data["name"] = $udata["nev"];
            $data["email"] = $udata["email"];

            $data["auth_token"] = hash('sha256', $data["neptun"] . $data["szektor"] . $data["utca"] . $data["felev"] . $cat);
            Database::run("INSERT INTO `authentication` (`auth_token`, `neptun_kod`, `szektor_id`, `utca_id`, `felev`, `created_at`) VALUES ('" . $data["auth_token"] . "', '" . $neptun . "', '" . $szektorutca[0] . "', '" . $szektorutca[1] . "', '" . $felev . "', '" . $cat . "')");

            if (empty($udata["nev"]) || empty($udata["email"])) {
                $data["code"] = 2;
                $data["modal"]["title"] = "Kedves Hallgató!";
                $data["modal"]["text"] = "Az alkalmazás használatához adategyeztetés szükséges, mivel most lépett be először a rendszerbe!";
                print json_encode($data, JSON_PRETTY_PRINT);
            } else {
                $data["code"] = 1;
                $data["modal"]["title"] = "Köszöntjük";
                $data["modal"]["text"] = "";
                print json_encode($data, JSON_PRETTY_PRINT);
            }
        }
        return;
    }
}