<?php
/**
 * Class Version
 * The Mobile application is synchronized with
 * the API-stored version and if the require, than
 * notify the user about the update possibility.
 * From the Admin panel the Developers
 * can modify this version number.
 *
 * We use Semantic Versioning
 *
 * Ex.: 1.122.4223
 * 1. MAJOR version when you make incompatible API changes,
 * 2. MINOR version when you add functionality in a backwards-compatible manner, and
 * 3. PATCH version when you make backwards-compatible bug fixes.
 */
abstract class Version extends BaseModule
{
    public static $version = "1.0.0";

    static public function get($params): void
    {
        $req = self::expectedParameters($params, ['number']);
        if (!$req[0]) {
            $info = array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")"
            );
        } else {
            $versioning = explode(".", $params["number"]);
            $lastVersion = @R::findAll('Version', ' ORDER BY major DESC, minor DESC, patch DESC LIMIT 1 ');
            $last = array();
            foreach ($lastVersion as $vcurrent) {
                $last = [$vcurrent->major, $vcurrent->minor, $vcurrent->patch];
                break;
            }
            if ($versioning[0] < $last[0]) {
                $info = array(
                    'code' => 3,
                    'message' => "Incompatible API changes. Please update the application to use!"
                );
            } else if ($versioning[1] < $last[1]) {
                $info = array(
                    'code' => 2,
                    'message' => "New functions implemented. Recommended, that update the application for to use new features."
                );
            } else if ($versioning[2] < $last[2]) {
                $info = array(
                    'code' => 1,
                    'message' => "Bug fixed. For more stability and more dependable please update the application."
                );
            } else {
                $info = array(
                    'code' => 0
                );
            }
            $info = array_merge($info, array("versions" => ["application" => implode(".", $versioning), "last" => implode(".", $last)]));
        }
        if (DEBUG) JSON::set("debug", array($params));
        JSON::set("Version", $info, self::$version);
    }

    static public function post($params): void
    {
        $req = self::expectedParameters($params, ['auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev', 'major', 'minor', 'patch', 'changes']);
        if (!$req[0]) {
            JSON::set("Version", array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")",
                'tip' => 'Log out the user, because an important parameter is missing.'
            ), self::$version);
            return;
        }
        $perm = AuthModel::checkPermission($params);
        if($perm==-1){
            $info = array(
                'code' => -1,
                'message' => "The authentication data of user is incorrect. Please log in again!"
            );
        }else if($perm>=2){
            $alreadyExists = @R::findOne('Version',
                ' major=? AND minor=? AND patch=?',
                [$params["major"],$params["minor"],$params["patch"]]
            );
            if($alreadyExists){
                $info = array(
                    'code' => 0,
                    'message' => "This version already is in database: ".$params["major"].".".$params["minor"].".".$params["patch"]
                );
            }else {
                R::exec('INSERT INTO `Version` (`major`, `minor`, `patch`, `changes`) VALUES (:major, :minor, :patch, :changes)',
                    [
                        ':major' => $params["major"],
                        ':minor' => $params["minor"],
                        ':patch' => $params["patch"],
                        ':changes' => $params["changes"],
                    ]
                );
                $info = array(
                    'code' => 1,
                    'major' => $params["major"],
                    'minor' => $params["minor"],
                    'patch' => $params["patch"],
                    'changes' => $params["changes"]
                );
            }
        }else{
            $info = array(
                'code' => 0,
                'message' => "You do not have enough permission for this."
            );
        }
        JSON::set("Version", $info, self::$version);
    }

    static public function delete($params): void
    {
        $req = self::expectedParameters($params, ['auth_token', 'neptun_kod', 'szektor_id', 'utca_id', 'felev', 'major', 'minor', 'patch']);
        if (!$req[0]) {
            JSON::set("Version", array(
                'code' => 400,
                'message' => "Bad Request, missing parameters (" . implode(", ", $req[1]) . ")",
                'tip' => 'Log out the user, because an important parameter is missing.'
            ), self::$version);
            return;
        }
        $perm = AuthModel::checkPermission($params);
        if($perm==-1){
            $info = array(
                'code' => -1,
                'message' => "The authentication data of user is incorrect. Please log in again!"
            );
        }else if($perm>=2){
            $alreadyExists = @R::findOne('Version',
                ' major=? AND minor=? AND patch=?',
                [$params["major"],$params["minor"],$params["patch"]]
            );
            if($alreadyExists){
                R::exec("DELETE FROM Version WHERE major=? AND minor=? AND patch=?",
                    [$params["major"],$params["minor"],$params["patch"]]);
                $info = array(
                    'code' => 0,
                    'message' => "The version (".$params["major"].".".$params["minor"].".".$params["patch"].") has been removed from the database"
                );
            }else {
                $info = array(
                    'code' => 0,
                    'message' => "The given version is not found in the database (".$params["major"].".".$params["minor"].".".$params["patch"].")"
                );
            }
        }else{
            $info = array(
                'code' => 0,
                'message' => "You do not have enough permission for this."
            );
        }
        JSON::set("Version", $info, self::$version);
    }

}