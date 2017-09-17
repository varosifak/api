<?php
/**
 * PHP Version 7.1
 *
 * @category Routing
 * @package  ApplicationProgrammingInterface
 * @author   Alex Szarka <xavvior@gmail.com>
 * @license  BY-NC-SA 4.0 | https://creativecommons.org/licenses/by-nc-sa/4.0/
 * @link     https://github.com/varosifak/api
 */
use \RedBeanPHP\R as R;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin");
header("Access-Control-Allow-Headers: X-Requested-With");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Headers: Accept");

require "config.php";
foreach ($_GET AS $key => $value) {
    $_GET[$key] = @addslashes(@htmlspecialchars($value));
}
foreach ($_COOKIE AS $key => $value) {
    $_COOKIE[$key] = @addslashes(@htmlspecialchars($value));
}
foreach ($_POST AS $key => $value) {
    $_POST[$key] = @addslashes(@htmlspecialchars($value));
}

spl_autoload_register(
    function ($class) {
        if (is_file("classes/" . $class . ".php")) {
            include_once 'classes/' . $class . '.php';
        }
        if (is_file("modules/" . $class . ".php")) {
            include_once 'modules/' . $class . '.php';
        }
        if (is_file("beans/" . $class . ".php")) {
            include_once 'beans/' . $class . '.php';
        }
    }
);

//R::setup(DB_TYPE . ':host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
R::setup('sqlite:'.DB_HOST.'-'.DB_NAME.'.db');

if (isset($_GET["action"])) {
    $className = ucfirst($_GET["action"]);
    if (is_file("modules/" . $className . ".php")) {
        new $className();
        return;
    } else {
        $data["error"]["code"] = 404;
        $data["error"]["message"] = "A modul nem található (".$_GET["action"].")";
    }
} else {
    $data["error"]["code"] = 422;
    $data["error"]["message"] = "Hiányzó paraméter az URL-ből (action)";
}
print json_encode($data, JSON_PRETTY_PRINT);
R::close();
?>
