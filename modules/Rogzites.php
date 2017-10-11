<?php

class Rogzites extends BaseModule
{
    public function __construct()
    {
        parent::__construct();
    }

    public function entryPoint()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $fp = @fopen('DATA.json', 'w');
        @fwrite($fp, json_encode($input));
        @fclose($fp);

        $authData = array(@$input["auth_token"], @$input["neptun"], @$input["szektor"], @$input["utca"], @$input["felev"]);
        $checkAuth = Database::select("SELECT * FROM authentication WHERE auth_token=? AND neptun_kod=? AND szektor_id=? AND utca_id=? AND felev=?", $authData);
        if (count($checkAuth) == 0) {
            $data["status"] = 0;
            $data["message"] = "A hitelesítés nem sikerült!";
        } else {
            $sfelev = Database::select("SELECT * FROM felevek WHERE felev = ?", array($input["felev"]))[0];
            if ($sfelev["active"] == 0) {
                $data["status"] = 2;
                $data["message"] = "A kiválasztott félév " . $sfelev["felev"] . " inaktív állapotban van, így adatot rá felvinni, módosítani nem lehet!";
            } else {
                $data["status"] = 1;
                $ctime = time();
                if (isset($_GET["bekuld"])) {
                    $image1B64 = $input["image1b64"];
                    $image2B64 = $input["image2b64"];
                    unset($input["image1b64"]);
                    unset($input["image2b64"]);

                    $json = json_encode($input, JSON_PRETTY_PRINT);
                    $darab = (Database::select("SELECT COUNT(*) AS db FROM fa_rogzitesek WHERE szektor_id=? AND utca_id=? AND felev=?", array($input["szektor"], $input["utca"], $input["felev"]))[0]["db"]) + 1;
                    if ($darab < 10) {
                        $darab = "00" . $darab;
                    } else if ($darab < 100) {
                        $darab = "0" . $darab;
                    }

                    $bekuld = $_GET["bekuld"] == "piszkozat" ? 1:0;
                    $tdb = Database::$db;
                    $bps = $tdb->prepare("INSERT INTO `fa_rogzitesek` (`neptun_kod`, `szektor_id`, `utca_id`, `felev`, `fa_egyedi_id`, `piszkozat`, `created_by`, `json`) VALUES (:neptun, :szektor, :utca, :felev, :fid, :piszkozat,:created, :json)");
                    $bps->bindParam(":neptun", $input["neptun"]);
                    $bps->bindParam(":szektor", $input["szektor"]);
                    $bps->bindParam(":utca", $input["utca"]);
                    $bps->bindParam(":felev", $input["felev"]);
                    $bps->bindParam(":piszkozat", $bekuld);
                    $bps->bindParam(":created", $ctime);
                    $bps->bindParam(":fid", $darab);
                    $bps->bindParam(":json", $json);
                    $bps->execute();
                    $data["sorszam"] = $input["szektor"] . $input["utca"] . $darab;

                    $k1 = $this->base64_to_jpeg($data["sorszam"], 1, $input["felev"], $image1B64);
                    $k2 = $this->base64_to_jpeg($data["sorszam"], 2, $input["felev"], $image2B64);
                    $k1 = $k1 != false ? $k1 : null;
                    $k2 = $k2 != false ? $k2 : null;

                    $tdb = Database::$db;
                    $bps = $tdb->prepare("UPDATE `fa_rogzitesek` SET image1 = :image1, image2 = :image2 WHERE szektor_id=:szektor_id AND utca_id=:utca_id AND felev=:felev AND fa_egyedi_id=:fid");
                    $bps->bindParam(":szektor_id", $input["szektor"]);
                    $bps->bindParam(":utca_id", $input["utca"]);
                    $bps->bindParam(":felev", $input["felev"]);
                    $bps->bindParam(":fid", $darab);
                    $bps->bindParam(":image1", $k1);
                    $bps->bindParam(":image2", $k2);
                    $bps->execute();
                }
            }
        }
        print json_encode($data);
    }

    private function base64_to_jpeg($sorszam, $iid, $felev)
    {
        $felev = str_replace("/", "-", $felev);
        if (!is_dir('images/'.$felev)) {
            mkdir('images/'.$felev, 0777, true);
        }
        $fname = "images/".$felev."/" . $sorszam . "_" . $iid . ".jpg";
        @unlink($fname);
        if (!empty($base64_string) && $base64_string != null) {
            $ifp = fopen($fname, 'wb');
            $data = explode(',', $base64_string);
            fwrite($ifp, base64_decode($data[1]));
            fclose($ifp);
            return $fname;
        }
        return false;
    }
}