<?php

abstract class Users extends BaseModule
{
    public static $version = "1.0.0";

    static public function get($params): void
    {

    }

    static public function post($params): void
    {
        $req = self::expectedParameters($params, ['neptun_kod', 'szektor_id', 'utca_id', 'felev', 'neptun', 'permission']);
        if (!$req[0]) {
            $info = array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")",
                'tip' => 'To use API, please read the documentation.'
            );
            JSON::set("Users", $info, self::$version);
            return;
        }
        $perm = AuthModel::checkPermission($params);
        if ($perm == -1) {
            $info = array(
                'code' => -1,
                'message' => "The authentication data of user is incorrect. Please log in again!"
            );
        } else if ($perm >= 1) {
            $alreadyExists = @R::findOne('Users',
                ' neptun=?',
                [$params["neptun"]]
            );
            if ($alreadyExists) {
                $info = array(
                    'code' => 0,
                    'message' => "This user already in database."
                );
            } else {
                R::exec("INSERT INTO `Users` (`neptun`, `permission`) VALUES (?, ?)",
                    [$params["neptun"], $params["permission"]]);
                $info = array(
                    'code' => 1,
                    'message' => "The user (" . $params["neptun"] . ") has been added into the database"
                );
            }
        } else {
            $info = array(
                'code' => 0,
                'message' => "You do not have enough permission for this."
            );
        }
        if (DEBUG) JSON::set("debug", array($params));
        JSON::set("Users", $info, self::$version);
    }

    static public function delete($params): void
    {
        $req = self::expectedParameters($params, ['auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev', 'neptun']);
        if (!$req[0]) {
            JSON::set("Users", array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")",
                'tip' => 'Log out the user, because an important parameter is missing.'
            ), self::$version);
            return;
        }
        $perm = AuthModel::checkPermission($params);
        if ($perm == -1) {
            $info = array(
                'code' => -1,
                'message' => "The authentication data of user is incorrect. Please log in again!"
            );
        } else if ($perm >= 2) {
            $alreadyExists = @R::findOne('Users',
                ' neptun=?',
                [$params["neptun"]]
            );
            if ($alreadyExists) {
                R::exec("DELETE FROM Users WHERE neptun=?",
                    [$params["neptun"]]);
                $info = array(
                    'code' => 1,
                    'message' => "The user (".$params["neptun"].") has been removed from database."
                );
            } else {
                $info = array(
                    'code' => 0,
                    'message' => "The given user not found in the database (" . $params["neptun"] . ")"
                );
            }
        } else {
            $info = array(
                'code' => 0,
                'message' => "You do not have enough permission for this."
            );
        }
        JSON::set("Users", $info, self::$version);
    }

    static public function patch($params): void
    {

    }
}