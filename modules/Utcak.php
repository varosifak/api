<?php

abstract class Utcak extends BaseModule
{
    public static $version = "1.0.0";

    static public function get($params): void
    {
        if(!empty($params["id"])){
            $instance = R::find('Utcak', ' id = ?', [$params["id"]]);
            if(count($instance)==0){
                JSON::set(get_class(), array(
                    'code' => 404,
                    'message' => "The street is not found."
                ), self::$version);
                return;
            }
            $results = [];
            foreach ($instance as $result){
                array_push($results, $result);
            }
            $info = array(
                'code' => 1,
                'result' => $results[0]
            );
        }else {
            $dataset = [];
            $list = R::findAll("Utcak", "ORDER BY id ASC");
            foreach ($list as $item) {
                array_push($dataset, $item);
            }
            $info = array(
                'code' => 1,
                'list' => $dataset
            );
        }
        if (DEBUG) JSON::set("debug", array($params));
        JSON::set(get_class(), $info, self::$version);
    }

    static public function post($params): void
    {
        $req = self::expectedParameters($params, ['neptun_kod', 'szektor_id', 'utca_id', 'felev', 'szektor_kod', 'utca_kod', 'utca_nev']);
        if (!$req[0]) {
            $info = array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")",
                'tip' => 'To use API, please read the documentation.'
            );
            JSON::set(get_class(), $info, self::$version);
            return;
        }
        $perm = AuthModel::checkPermission($params);
        if ($perm == -1) {
            $info = array(
                'code' => -1,
                'message' => "The authentication data of user is incorrect. Please log in again!"
            );
        } else if ($perm >= 1) {
            $alreadyExists = @R::findOne('Utcak',
                ' szektor_kod=? OR utca_kod=? OR utca_nev=?',
                [$params["szektor_kod"], $params["utca_kod"], $params["utca_nev"]]
            );
            if ($alreadyExists) {
                $info = array(
                    'code' => 0,
                    'message' => "This ".get_class()." already in database."
                );
            } else {
                R::exec("INSERT INTO `Utcak` (`szektor_kod`, `utca_kod`, `utca_nev`) VALUES (?, ?, ?)",
                    [$params["szektor_kod"], $params["utca_kod"], $params["utca_nev"]]);
                $info = array(
                    'code' => 1,
                    'message' => "The ".get_class()." (" . $params["utca_nev"] . ") has been added into the database"
                );
            }
        } else {
            $info = array(
                'code' => 0,
                'message' => "You do not have enough permission for this."
            );
        }
        if (DEBUG) JSON::set("debug", array($params));
        JSON::set(get_class(), $info, self::$version);
    }
    static public function delete($params): void
    {
        $req = self::expectedParameters($params, ['auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev', 'id']);
        if (!$req[0]) {
            JSON::set(get_class(), array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")"
            ), self::$version);
            return;
        }
        $instance = R::findOne(get_class(),
            ' id=?',
            [$params["id"]]);
        $perm = AuthModel::checkPermission($params);
        if ($perm == -1) {
            $info = array(
                'code' => -1,
                'message' => "The authentication data of user is incorrect. Please log in again!"
            );
        } else if ($perm >= 1) {
            if ($instance == NULL) {
                $info = array(
                    'code' => 404,
                    'message' => "The ".get_class()." not found, so we can not delete."
                );
            } else {
                R::trash($instance);
                $info = array(
                    'code' => 200,
                    'message' => "The ".get_class()." was successful deleted."
                );
            }
        } else {
            $info = array(
                'code' => 0,
                'message' => "You do not have enough permission for this."
            );
        }
        if (DEBUG) JSON::set("debug", array($params));
        JSON::set(get_class(), $info, self::$version);
    }
    static public function patch($params): void
    {
        $req = self::expectedParameters($params, ['auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev', 'id', 'szektor_kod', 'utca_kod', 'utca_nev']);
        if (!$req[0]) {
            $info = array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")",
                'tip' => 'To use API, please read the documentation.'
            );
            JSON::set(get_class(), $info, self::$version);
            return;
        }
        $perm = AuthModel::checkPermission($params);
        if ($perm == -1) {
            $info = array(
                'code' => -1,
                'message' => "The authentication data of user is incorrect. Please log in again!"
            );
        } else if ($perm >= 1) {
            $record = @R::findOne(get_class(),
                ' id=?',
                [$params["id"]]
            );
            if ($record) {
                $record->szektor_kod = $params["szektor_kod"];
                $record->utca_kod = $params["utca_kod"];
                $record->utca_nev = $params["utca_nev"];
                R::store($record);
                $info = array(
                    'code' => 1,
                    'message' => "The ".get_class()." (" . $params["utca_nev"] . ") has been updated."
                );
            } else {
                $info = array(
                    'code' => 0,
                    'message' => "The ".get_class()." (" . $params["utca_nev"] . ") not found."
                );
            }
        } else {
            $info = array(
                'code' => 0,
                'message' => "You do not have enough permission for this."
            );
        }
        if (DEBUG) JSON::set("debug", array($params));
        JSON::set(get_class(), $info, self::$version);
    }
}