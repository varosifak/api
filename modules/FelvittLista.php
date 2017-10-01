<?php

/**
 * Created by PhpStorm.
 * User: xavvi
 * Date: 2017. 09. 29.
 * Time: 12:34
 */
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
            if(isset($_GET["action"]) && $_GET["action"]=="piszkozat"){
                $data["trees"] = $this->reviewList();
            }else{
                $data["trees"] = $this->acceptedList();
            }
            if(count($data["trees"])==0) {
                $data["status"] = 2;
                $data["message"] = "Még egy felvitt fa sincs az adatbázisban!";
            }
        }
        print json_encode($data);
    }

    public function reviewList(){
        //TODO
    }

    public function acceptedList(){
        $falista = Database::select("SELECT * FROM fa_rogzitesek WHERE szektor_id = ? AND utca_id = ? AND felev = ?",
            array($_GET["szektor"], $_GET["utca"], $_GET["felev"])
        );

        return $falista;
    }
}