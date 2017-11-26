<?php

abstract class UsersUtcak extends BaseModule
{
    public static $version = "1.0.0";

    static public function get($params)
    {
        $req = self::expectedParameters($params, ['neptun_kod', 'szektor_id', 'utca_id', 'felev']);
        if (!$req[0]) {
            $info = array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")",
                'tip' => 'To use API, please read the documentation.'
            );
            JSON::set(get_class(), $info, self::$version);
        }

        $perm = AuthModel::checkPermission($params);
        if ($perm == -1) {
            $info = array(
                'code' => -1,
                'message' => "The authentication data of user is incorrect. Please log in again!"
            );
        } else if ($perm >= 1) {
            $list = R::getAll('select uu.neptun_kod, u.nev, uu.felev, ut.utca_nev from UsersUtcak uu INNER JOIN Users u ON u.neptun = uu.neptun_kod INNER JOIN Utcak ut ON uu.szektor_id=ut.szektor_kod AND uu.utca_id=ut.utca_kod ORDER BY felev DESC, szektor_id ASC, utca_kod ASC');
            $plist = [];
            foreach ($list as $item) {
                array_push($plist, $item);
            }
            $info = array(
                'code' => 1,
                'message' => "Listing users",
                'list' => $plist
            );
        } else {
            $list = R::getAll('select uu.neptun_kod, u.nev from UsersUtcak uu INNER JOIN Users u ON u.neptun = uu.neptun_kod  WHERE felev=? AND szektor_id=? AND utca_id=?', [
                $params["felev"], $params["szektor_id"], $params["utca_id"]
            ]);
            $plist = [];
            foreach ($list as $item) {
                array_push($plist, $item);
            }
            $info = array(
                'code' => 1,
                'message' => "Listing partners of " . $params["felev"] . " semester of " . $params["szektor_id"] . "" . $params["utca_id"],
                'list' => $plist
            );
        }
        JSON::set(get_class(), $info, self::$version);
    }

    static public function post($params)
    {
        $req = self::expectedParameters($params, ['auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev', 'addneptun', 'addszektor', 'addutca', 'addfelev']);
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
            $alreadyExists = @R::findOne(get_class(),
                ' neptun_kod = ? AND szektor_id=? AND utca_id=? AND felev=?',
                [$params["addneptun"], $params["addszektor"], $params["addutca"], $params["addfelev"]]
            );
            if ($alreadyExists) {
                $info = array(
                    'code' => 0,
                    'message' => "This " . get_class() . " already in database."
                );
            } else {
                R::exec("INSERT INTO `UsersUtcak` (`neptun_kod`, `szektor_id`, `utca_id`, `felev`) VALUES (?, ?, ?, ?)",
                    [$params["addneptun"], $params["addszektor"], $params["addutca"], $params["addfelev"]]
                );
                $info = array(
                    'code' => 1,
                    'message' => "The User-Utca connection has been added into the database"
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
                    'message' => "The " . get_class() . " not found, so we can not delete."
                );
            } else {
                R::trash($instance);
                $info = array(
                    'code' => 200,
                    'message' => "The " . get_class() . " was successful deleted."
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