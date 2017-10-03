<?php
class FelvittLista extends BaseModule
{
    public function __construct()
    {
        parent::__construct();
    }

    public function entryPoint()
    {
        $authData = array(@$_GET["auth_token"], @$_GET["neptun"], @$_GET["szektor"], @$_GET["utca"], @$_GET["felev"]);
        $checkAuth = Database::select("SELECT * FROM authentication WHERE auth_token=? AND neptun_kod=? AND szektor_id=? AND utca_id=? AND felev=?", $authData);
        if(count($checkAuth)==0){
            $data["status"] = 0;
            $data["message"] = "A hitelesítés nem sikerült!";
        }else{
            if(isset($_GET["action2"]) && $_GET["action2"]=="piszkozat"){
                $data["trees"] = $this->reviewList();
            }else{
                $data["trees"] = $this->acceptedList();
            }
            if(count($data["trees"])==0) {
                $data["status"] = 2;
                if(isset($_GET["action2"]) && $_GET["action2"]=="piszkozat") {
                    $data["message"] = "Nincsenek döntésre váró piszkozatok!";
                }else{
                    $data["message"] = "Még egy felvitt fa sincs az adatbázisban!";
                }
            }
        }
        print json_encode($data, JSON_PRETTY_PRINT);
    }

    public function reviewList(){
        $falista = Database::select("SELECT fa_rogzitesek.*, users.nev FROM fa_rogzitesek INNER JOIN users ON users.neptun=fa_rogzitesek.neptun_kod WHERE szektor_id = ? AND utca_id = ? AND felev = ? AND piszkozat=1 ORDER BY fa_egyedi_id DESC",
            array($_GET["szektor"], $_GET["utca"], $_GET["felev"])
        );
        for($i=0;$i<count($falista);$i++){
            $falista[$i]["json"] = json_decode($falista[$i]["json"]);
        }
        return $falista;
    }

    public function acceptedList(){
        $falista = Database::select("SELECT fa_rogzitesek.*, users.nev FROM fa_rogzitesek INNER JOIN users ON users.neptun=fa_rogzitesek.neptun_kod WHERE szektor_id = ? AND utca_id = ? AND felev = ? AND piszkozat=0 ORDER BY fa_egyedi_id DESC",
            array($_GET["szektor"], $_GET["utca"], $_GET["felev"])
        );
        for($i=0;$i<count($falista);$i++){
            $falista[$i]["json"] = json_decode($falista[$i]["json"]);
            $fareszlet = Database::select("SELECT * FROM fa_fajok WHERE id='".$falista[$i]["json"]->fa_fajta."'");
            $falista[$i]["fareszlet"] = $fareszlet[0];
        }

        return $falista;
    }
}