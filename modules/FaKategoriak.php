<?php

abstract class FaKategoriak extends BaseModule
{
    public static $version = "1.0.0";

    static public function get($params)
    {
        if (!empty($params["id"])) {
            $instance = R::find('FaKategoriak', ' id = ?', [$params["id"]]);
            if (count($instance) == 0) {
                JSON::set(get_class(), array(
                    'code' => 404,
                    'message' => "The category is not found."
                ), self::$version);
                return;
            }
            $results = [];
            foreach ($instance as $result) {
                array_push($results, $result);
            }
            $info = array(
                'code' => 1,
                'result' => $results[0]
            );
        } else {
            $dataset = [];
            $list = R::findAll("FaKategoriak", "ORDER BY id ASC");
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

    static public function post($params)
    {
        $req = self::expectedParameters($params, ['neptun_kod', 'szektor_id', 'utca_id', 'felev', 'kategoria']);
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
            $alreadyExists = @R::findOne('FaKategoriak',
                ' kategoria=?',
                [$params["kategoria"]]
            );
            if ($alreadyExists) {
                $info = array(
                    'code' => 0,
                    'message' => "This category already in database."
                );
            } else {
                R::exec("INSERT INTO `FaKategoriak` (`kategoria`) VALUES (?)",
                    [$params["kategoria"]]);
                $info = array(
                    'code' => 1,
                    'message' => "The Category (" . $params["magyar"] . ") has been added into the database"
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
        $req = self::expectedParameters($params, ['auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev', 'id']);
        if (!$req[0]) {
            JSON::set(get_class(), array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")"
            ), self::$version);
            return;
        }
        $instance = R::findOne(
            'FaKategoriak',
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
                    'message' => "The category not found, so we can not delete."
                );
            } else {
                R::trash($instance);
                $info = array(
                    'code' => 200,
                    'message' => "The category was successful deleted."
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
        $req = self::expectedParameters($params, ['neptun_kod', 'szektor_id', 'utca_id', 'felev', 'id', 'kategoria']);
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
            $record = @R::findOne('FaKategoriak',
                ' id=?',
                [$params["id"]]
            );
            if ($record) {
                $record->kategoria = $params["kategoria"];
                R::store($record);
                $info = array(
                    'code' => 1,
                    'message' => "The category (" . $params["kategoria"] . ") has been updated."
                );
            } else {
                $info = array(
                    'code' => 0,
                    'message' => "The tree type (" . $params["kategoria"] . ") not found."
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