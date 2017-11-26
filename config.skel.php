<?php
/**
 * PHP Version 7.1
 *
 * @category Configuration
 * @package  ApplicationProgrammingInterface
 * @author   Alex Szarka <xavvior@gmail.com>
 * @license  BY-NC-SA 4.0 | https://creativecommons.org/licenses/by-nc-sa/4.0/
 * @link     https://github.com/varosifak/api
 */
define("DB_TYPE",               "mysql");
define("DB_HOST",               "");
define("DB_USER",               "");
define("DB_PASS",               "");
define("DB_NAME",               "");
define("DB_CHARSET",            "utf8");
define("MAINTENANCE",           false);
define("DEBUG",                 false);
define("DB_CONNECTION",         DB_TYPE . ':host=' . DB_HOST . ";dbname=" . DB_NAME);
?>