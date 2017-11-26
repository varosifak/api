<?php

abstract class Felevek extends BaseModule
{
    public static $version = "1.0.0";

    static public function get($params): void
    {
        $idoszak = array("felevek" => []);
        $felevek = R::findAll('Felevek', ' ORDER BY felev DESC ');
        foreach ($felevek as $felev) {
            $temp = array("id" => $felev->id, "felev" => $felev->felev, "informaciok" => $felev->informaciok, "active" => $felev->active);
            array_push($idoszak["felevek"], $temp);
        }
        if (DEBUG) JSON::set("debug", array($params));
        JSON::set(get_class(), $idoszak, self::$version);
    }

    static public function post($params): void
    {
        $req = self::expectedParameters($params, ['neptun_kod', 'szektor_id', 'utca_id', 'felev', 'createFelev', 'createInformaciok', 'createActive']);
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
            $alreadyExists = @R::findOne('Felevek',
                ' felev=?',
                [$params["createFelev"]]
            );
            if ($alreadyExists) {
                $info = array(
                    'code' => 0,
                    'message' => "This period already added into database."
                );
            } else {
                R::exec("INSERT INTO `Felevek` (`felev`, `informaciok`, `active`) VALUES (?, ?, ?)",
                    [$params["createFelev"], $params["createInformaciok"], $params["createActive"]]);
                $info = array(
                    'code' => 1,
                    'message' => "The period (" . $params["createFelev"] . ") has been added into the database"
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
        $req = self::expectedParameters($params, ['neptun_kod', 'szektor_id', 'utca_id', 'felev', 'updateFelev', 'updateNewFelev', 'updateInformaciok', 'updateActive']);
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
            $record = @R::findOne('Felevek',
                ' felev=?',
                [$params["updateFelev"]]
            );
            if ($record) {
                $record->felev = $params["updateNewFelev"];
                $record->informaciok = $params["updateInformaciok"];
                $record->active = $params["updateActive"];
                R::store($record);
                $info = array(
                    'code' => 1,
                    'message' => "The period (" . $params["updateFelev"] . ") has been updated."
                );
            } else {
                $info = array(
                    'code' => 0,
                    'message' => "The period (" . $params["updateFelev"] . ") not found."
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
        $req = self::expectedParameters($params, ['auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev', 'deleteFelev']);
        if (!$req[0]) {
            JSON::set(get_class(), array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")"
            ), self::$version);
            return;
        }
        $felevData = R::findOne(
            'Felevek',
            ' felev=?',
            [$params["deleteFelev"]]);
        $perm = AuthModel::checkPermission($params);
        if ($perm == -1) {
            $info = array(
                'code' => -1,
                'message' => "The authentication data of user is incorrect. Please log in again!"
            );
        } else if ($perm >= 1) {
            if ($felevData == NULL) {
                $info = array(
                    'code' => 404,
                    'message' => "The period not found, so we can not delete."
                );
            } else {
                R::trash($felevData);
                $info = array(
                    'code' => 200,
                    'message' => "The period was successful deleted."
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