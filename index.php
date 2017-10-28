<?php

use \MicroLight\Components\Configuration as Configuration;
use \MicroLight\Components\JSON as JSON;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/json; charset=utf-8');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
require_once "autoloader.php";
require_once "rb.php";
Configuration::loadConfig("config.php");
Configuration::serializeRequests($_GET);
R::setup(DB_CONNECTION, DB_USER, DB_PASS);
$user = new User();
$user->save();
print json_encode(JSON::get(), JSON_PRETTY_PRINT);
R::close();