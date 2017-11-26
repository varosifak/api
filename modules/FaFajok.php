<?php

abstract class FaFajok extends BaseModule
{
    public static $version = "1.0.0";

    static public function get($params)
    {
        if (!empty($params["id"])) {
            $faj = R::find('FaFajok', ' id = ?', [$params["id"]]);
            if (count($faj) == 0) {
                JSON::set(get_class(), array(
                    'code' => 404,
                    'message' => "The tree is not found."
                ), self::$version);
                return;
            }
            $fajResult = [];
            foreach ($faj as $fajItem) {
                array_push($fajResult, $fajItem);
            }
            $info = array(
                'code' => 1,
                'result' => $fajResult[0]
            );
        } else {
            $dataset = [];
            $list = R::findAll("FaKategoriak", "ORDER BY id ASC");
            foreach ($list as $item) {
                $fajok = R::find('FaFajok', ' kategoria = ? ORDER BY magyar ASC', [$item->kategoria]);
                $fajListaParsed = [];
                foreach ($fajok as $faj) {
                    array_push($fajListaParsed, $faj);
                }
                array_push($dataset, array($item->kategoria => $fajListaParsed));
            }
            $info = array(
                'code' => 1,
                'list' => $dataset
            );
        }
        if (DEBUG) JSON::set("debug", array($params));
        JSON::set(get_class(), $info, self::$version);
    }

    static public function post($params)
    {
        $req = self::expectedParameters($params, ['neptun_kod', 'szektor_id', 'utca_id', 'felev', 'kategoria', 'magyar', 'latin', 'megjegyzes', 'kepek']);
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
            $alreadyExists = @R::findOne('FaFajta',
                ' magyar=? OR latin=?',
                [$params["magyar"], $params["latin"]]
            );
            if ($alreadyExists) {
                $info = array(
                    'code' => 0,
                    'message' => "This Tree type already in database."
                );
            } else {
                R::exec("INSERT INTO `FaFajok` (`kategoria`, `magyar`, `latin`, `megjegyzes`, `kepek`) VALUES (?, ?, ?, ?, ?)",
                    [$params["kategoria"], $params["magyar"], $params["latin"], $params["megjegyzes"], $params["kepek"]]);
                $info = array(
                    'code' => 1,
                    'message' => "The TreeType (" . $params["magyar"] . ") has been added into the database"
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

    static public function delete($params)
    {
        $req = self::expectedParameters($params, ['auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev', 'fafajid']);
        if (!$req[0]) {
            JSON::set(get_class(), array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")"
            ), self::$version);
            return;
        }
        $faFajPeldany = R::findOne(
            'FaFajok',
            ' id=?',
            [$params["fafajid"]]);
        $perm = AuthModel::checkPermission($params);
        if ($perm == -1) {
            $info = array(
                'code' => -1,
                'message' => "The authentication data of user is incorrect. Please log in again!"
            );
        } else if ($perm >= 1) {
            if ($faFajPeldany == NULL) {
                $info = array(
                    'code' => 404,
                    'message' => "The instance of ".get_class()." is not found, so we can not delete."
                );
            } else {
                R::trash($faFajPeldany);
                $info = array(
                    'code' => 200,
                    'message' => "The tree type was successful deleted."
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

    static public function patch($params)
    {
        $req = self::expectedParameters($params, ['neptun_kod', 'szektor_id', 'utca_id', 'felev', 'id', 'kategoria', 'magyar', 'latin', 'megjegyzes', 'kepek']);
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
            $record = @R::findOne('FaFajok',
                ' id=?',
                [$params["id"]]
            );
            if ($record) {
                $record->kategoria = $params["kategoria"];
                $record->magyar = $params["magyar"];
                $record->latin = $params["latin"];
                $record->megjegyzes = $params["megjegyzes"];
                $record->kepek = $params["kepek"];
                R::store($record);
                $info = array(
                    'code' => 1,
                    'message' => "The tree type (" . $params["magyar"] . ") has been updated."
                );
            } else {
                $info = array(
                    'code' => 0,
                    'message' => "The tree type (" . $params["magyar"] . ") not found."
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