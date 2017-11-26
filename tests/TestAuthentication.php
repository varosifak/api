<?php
declare(strict_types=1);
include_once(__DIR__ . "/../config.php");
include_once(__DIR__ . "/autoload.php");
include_once(__DIR__ . "/../rb.php");
include_once(__DIR__ . "/ReferenceLoader.php");

use PHPUnit\Framework\TestCase;

class TestAuthentication extends TestCase
{
    public function testInvalidAuthenticate()
    {
        R::setup(DB_CONNECTION, DB_USER, DB_PASS);
        $params = [
            'auth_token' => 'invalidauthtoken',
            'neptun_kod' => 'user',
            'szektor_id' => 'szektor',
            'utca_id' => 'utca',
            'felev' => '2017-2018/1'
        ];
        Authentication::get($params);
        $reference = new ReferenceLoader("authentication-invalid-token.json");
        $this->assertEquals(
            json_encode($reference->get(), JSON_PRETTY_PRINT),
            json_encode(JSON::get(), JSON_PRETTY_PRINT)
        );
        R::close();
    }
    /*public function testLoginAuthenticate()
    {
        R::setup(DB_CONNECTION, DB_USER, DB_PASS);
        $params = [
            'neptun_kod' => 'user',
            'szektor_id' => 'szektor',
            'utca_id' => 'utca',
            'felev' => '2017-2018/1'
        ];
        Authentication::post($params);
        $reference = new ReferenceLoader("authentication-invalid-token.json");
        $this->assertEquals(
            json_encode($reference->get(), JSON_PRETTY_PRINT),
            json_encode(JSON::get(), JSON_PRETTY_PRINT)
        );
        R::close();
    }*/

}