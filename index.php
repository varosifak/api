<?php
use \MicroLight\Components\Configuration as Configuration;
use \MicroLight\Components\JSON as JSON;
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/json; charset=utf-8');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
require_once "autoloader.php";
require_once "MicroLight/Components/rb.php";

Configuration::loadConfig("config.php");
Configuration::serializeRequests($_GET);

R::setup();
print json_encode(JSON::get(), JSON_PRETTY_PRINT);
?>