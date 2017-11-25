<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/json; charset=utf-8');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
require_once "autoloader.php";
require_once "rb.php";
Configuration::loadConfig("config.php");
Configuration::serializeRequests($_GET);
R::setup(DB_CONNECTION, DB_USER, DB_PASS);
if (MAINTENANCE) {
    JSON::set('maintenance', array('code' => 403, 'message' => 'There is currently maintenance. Try again later.'));
} else {
    require "routing.php";
}
print json_encode(JSON::get(), JSON_PRETTY_PRINT);
R::close();