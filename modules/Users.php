<?php

abstract class Users extends BaseModule
{
    public static $version = "1.0.0";

    static public function get($params): void
    {
        $req = self::expectedParameters($params, ['auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev']);
        if (!$req[0]) {
            $info = array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")",
                'tip' => 'To use API, please read the documentation.'
            );
            JSON::set(get_class(), $info, self::$version);
            return;
        }
        if (AuthModel::checkAuthenticated($params) == NULL) {
            JSON::set(get_class(), array(
                'code' => 404,
                'message' => "The data of authentication parameters is not found.",
                'tip' => 'We recommend, that log out the user from the session.'
            ), self::$version);
            return;
        }
        $userData = @R::findOne('Users',
            ' neptun=?',
            [$params["neptun_kod"]]
        );
        $informations = array(
            'neptun' => $userData->neptun,
            'nev' => $userData->nev,
            'email' => $userData->email,
            'permission' => $userData->permission
        );
        JSON::set(get_class(), $informations, self::$version);
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
        JSON::set(get_class(), $info, self::$version);
    }

    static public function delete($params): void
    {
        $req = self::expectedParameters($params, ['auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev', 'neptun']);
        if (!$req[0]) {
            JSON::set(get_class(), array(
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
                    'message' => "The user (" . $params["neptun"] . ") has been removed from database."
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
        JSON::set(get_class(), $info, self::$version);
    }

    static public function patch($params): void
    {
        function save($params)
        {
            R::exec("UPDATE `Users` SET `nev`=?, `email`=? WHERE (`neptun`=?)",
                [$params["nev"], $params["email"], $params["neptun_kod"]]
            );
            return array(
                'code' => 1,
                'message' => "The user (" . $params["neptun_kod"] . ") has been updated."
            );
        }

        $req = self::expectedParameters($params, ['neptun_kod', 'szektor_id', 'utca_id', 'felev', 'nev', 'email']);
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
            if (!empty($params["neptun"])) {
                $modifyUser = @R::findOne('Users',
                    ' neptun=?',
                    [$params["neptun"]]
                );
                if ($modifyUser) {
                    R::exec("UPDATE `Users` SET `nev`=?, `email`=?, `permission`=? WHERE (`neptun`=?)",
                        [$params["nev"], $params["email"], (!empty($params["permission"]) ? $params["permission"] : $modifyUser->permission), $params["neptun"]]
                    );
                    $info = array(
                        'code' => 1,
                        'message' => "The user (" . $params["neptun"] . ") has been updated."
                    );
                } else {
                    $info = array(
                        'code' => 400,
                        'message' => "Bad Request, missing parameter (neptun)",
                        'tip' => 'To use API, please read the documentation.'
                    );
                }
            } else {
                $info = array(
                    'code' => 0,
                    'message' => "Missing parameter (" . $params["neptun"] . ")"
                );
            }
        } else {
            if (!empty($params["neptun"])) {
                if ($params["neptun_kod"] == $params["neptun"]) {
                    $info = save($params);
                } else {
                    $info = array(
                        'code' => 0,
                        'message' => "You do not have enough permission for this."
                    );
                }
            } else {
                $info = save($params);
            }
        }
        JSON::set(get_class(), $info, self::$version);
    }

    static public function propfind($params): void
    {
        function protectColumns($columnList, $protecteds, $permission)
        {
            $columns = [];
            foreach ($columnList as $column) {
                if (!in_array($column, $protecteds) || $permission>=1) {
                    array_push($columns, $column);
                }
            }
            return $columns;
        }
        function Listing($params, $permission){
            $columns = protectColumns($params["columns"], ['neptun', 'email'], $permission);
            if (empty($params["conditions"])) {
                $params["conditions"] = "1=1";
            }
            if (empty($params['orderby'])) {
                $params['orderby'] = "nev ASC";
            }

            $query = "SELECT " . implode(",", $columns) . " FROM Users WHERE " . $params["conditions"] . " ORDER BY " . $params["orderby"];
            $listResult = R::getAll($query);
            return $listResult;
        }

        $req = self::expectedParameters($params, ['neptun_kod', 'szektor_id', 'utca_id', 'felev', 'columns', 'conditions', 'orderby']);
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
            JSON::set(get_class(), array(
                'code' => 0,
                'message' => "The authentication data of user is incorrect. Please log in again!"
            ), self::$version);
            return;
        }
        $list = array("code" => 1, "List" => Listing($params, $perm));

        JSON::set(get_class(), $list, self::$version);
    }
}