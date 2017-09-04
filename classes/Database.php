<?php

class Database
{
    public static $db = null;

    public static function Connect($host, $dbname, $username, $password)
    {
        try {
            $db = @new PDO('mysql:host=' . $host . ';dbname=' . $dbname, $username, $password,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
            Database::$db = $db;
            return true;
        } catch (Exception $e) {
            $data["error"]["code"] = 403;
            $data["error"]["message"] = $e->getMessage();
            print json_encode($data, JSON_PRETTY_PRINT);
            die;
        }
    }

    public static function select($query, $additionalInfo = null)
    {
        $sth = Database::$db->prepare($query);
        $sth->execute($additionalInfo);
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function run($query, $debug = false)
    {
        try {
            Database::$db->exec($query);
            return true;
        } catch (Exception $e) {
            return $debug ? $e : false;
        }
    }

    public static function db()
    {
        return Database::$db;
    }
}