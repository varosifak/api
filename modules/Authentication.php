<?php

abstract class Authentication extends BaseModule
{
    public static $version = "1.0.0";

    static public function get($params): void
    {
        $req = self::expectedParameters($params, ['auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev']);
        if (!$req[0]) {
            $info = array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")",
                'tip' => 'Log out the user, because an important parameter is missing.'
            );
        } else {
            $dataOfAuthParams = AuthModel::checkAuthenticated($params);
            if ($dataOfAuthParams == NULL) {
                JSON::set(get_class(), array(
                    'code' => 404,
                    'message' => "The data of authentication parameters is not found.",
                    'tip' => 'We recommend, that log out the user from the session.'
                ), self::$version);
                return;
            }
            $info = array(
                'code' => 200,
                'message' => "The user has been successful authenticated.",
                'neptun_kod' => $dataOfAuthParams->neptun_kod,
                'szektor_id' => $dataOfAuthParams->szektor_id,
                'utca_id' => $dataOfAuthParams->utca_id,
                'felev' => $dataOfAuthParams->felev,
                'help' => 'Alright. Nothing to do.'
            );
        }
        if (DEBUG) JSON::set("debug", array($params));
        JSON::set(get_class(), $info, self::$version);
    }

    static public function post($params): void
    {
        $req = self::expectedParameters($params, ['neptun_kod', 'szektor_id', 'utca_id', 'felev']);
        if (!$req[0]) {
            $info = array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")",
                'tip' => 'To use API, please read the documentation.'
            );
            JSON::set(get_class(), $info, self::$version);
            return;
        }
        $user = R::findOne('UsersUtcak', ' neptun_kod = ? AND szektor_id = ? AND utca_id = ? AND felev = ?',
            [$params["neptun_kod"], $params["szektor_id"], $params["utca_id"], $params["felev"]]
        );
        if ($user != NULL) {
            R::exec('INSERT INTO `Authentication` (`auth_token`, `neptun_kod`, `szektor_id`, `utca_id`, `felev`, `createdAt`) VALUES (:auth_token, :neptun_kod, :szektor_id, :utca_id, :felev, :createdAt)',
                [
                    ':auth_token' => hash('sha256', $params["neptun_kod"] . $params["szektor_id"] . $params["utca_id"] . $params["felev"] . time()),
                    ':neptun_kod' => $params["neptun_kod"],
                    ':szektor_id' => $params["szektor_id"],
                    ':utca_id' => $params["utca_id"],
                    ':felev' => $params["felev"],
                    ':createdAt' => time()
                ]
            );
            $id = R::getInsertID();
            $authdata = R::findOne("Authentication", " id = ?", [$id]);
            $info = array(
                'code' => 200,
                'message' => 'Login has been successful.',
                'auth_token' => $authdata->auth_token,
                'neptun_kod' => $authdata->neptun_kod,
                'szektor_id' => $authdata->szektor_id,
                'utca_id' => $authdata->utca_id,
                'felev' => $authdata->felev,
                'help' => 'Alright, log in the user into User interface'
            );
        } else {
            $info = array(
                'code' => 401,
                'message' => 'The user informations is incorrect.',
                'help' => 'The login is refused, because the given user informations was invalid. No log in.'
            );
        }
        if (DEBUG) JSON::set("debug", array($params));
        JSON::set(get_class(), $info, self::$version);
    }

    static public function delete($params): void
    {
        $req = self::expectedParameters($params, ['auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev']);
        if (!$req[0]) {
            JSON::set(get_class(), array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")",
                'tip' => 'Log out the user, because an important parameter is missing.'
            ), self::$version);
            return;
        }
        $dataOfAuthParams = R::findOne(
            'Authentication',
            ' auth_token=? AND neptun_kod=? AND szektor_id=? AND utca_id=? AND felev=?',
            [$params["auth_token"], $params["neptun_kod"], $params["szektor_id"], $params["utca_id"], $params["felev"]]);
        if ($dataOfAuthParams == NULL) {
            $info = array(
                'code' => 404,
                'message' => "The data of authentication parameters is not found.",
                'help' => 'Log out user from session, because the authentication data is not found.'
            );
        } else {
            R::trash($dataOfAuthParams);
            $info = array(
                'code' => 200,
                'message' => "The user has been successful logged out.",
                'help' => 'Alright. Nothing to do.'
            );
        }
        if (DEBUG) JSON::set("debug", array($params));
        JSON::set(get_class(), $info, self::$version);
    }
}