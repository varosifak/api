<?php
abstract class AuthModel
{
    static public function checkAuthenticated($params) {
        $result = R::findOne('Authentication',
            ' auth_token=? AND neptun_kod=? AND szektor_id=? AND utca_id=? AND felev=?',
            [$params["auth_token"], $params["neptun_kod"], $params["szektor_id"], $params["utca_id"], $params["felev"]]);
        return $result;
    }

    static public function checkPermission($params): int {
        $auth = self::checkAuthenticated($params);
        if($auth==NULL)
            return -1;
        $result = @R::findOne('Users',
            ' neptun=?',
            [$params["neptun_kod"]]
        );
        return $result->permission;
    }
}