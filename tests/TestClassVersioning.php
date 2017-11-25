<?php
declare(strict_types=1);
include_once(__DIR__."/autoload.php");
include_once(__DIR__."/../rb.php");
use PHPUnit\Framework\TestCase;
use MicroLight\Components\JSON as JSON;
use MicroLight\Components\Configuration as Configuration;
use MicroLight\Components\Router as Router;

class TestClassVersioning extends TestCase
{
    /**
     * The first element of the array is the class,
     * the second element, the generated UUID.
     * If the version number of class is changed, require
     * renew the UUID.
     */
    private $classes = [
        array(JSON::class, '1.0.1-082fc90a216bcf0231769f0631c2aa4da40e74fc'),
        array(Configuration::class, '1.0.1-38b6d998a4a5c63d5a923f75dde8e89f2b39702a'),
        array(Router::class, '1.0.0-904d2e1dedb36d0cb4d0b8aeb2df4d6ec05289cc'),
        array(BeansBase::class, '1.0.0-0de1927f102f3047cda4d3beb3a127fe00246a5d'),
        array(Authentication::class, '1.0.0-86980779726665ce08b266a0fab64e734622a673'),
        array(User::class, '0.0.1-ce5fcd2437f6bc585927d008662de507f9ed9ecf'),
        array(BaseModule::class, '1.0.1-cbcdb96774c252e3a76dac89e6abb1b22e32221d'),
        array(Main::class, '1.0.1-36a70a1dda7882ff5ea6044c11f2682bebdd59d1'),
    ];

    public function testClassVersions()
    {
        foreach ($this->classes as $classInformations) {
            $generatedUUID = $classInformations[0]::$version . "-" . hash(
                    'sha1', $classInformations[0] . $classInformations[0]::$version, false
                );

            $this->assertEquals(
                $classInformations[1], $generatedUUID,
                $classInformations[0].' version error: The version number ' .
                'of the class has been inconsistent. ' .
                'Please update the UUID of the ' . $classInformations[0] . ' class in the ' .
                'tests/TestClassVersioning.php file to ' . $generatedUUID
            );
        }
    }

}