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
        array(JSON::class, '1.0.0-f4ffdf8390ebb1c203b389906b45a1e28931df67'),
        array(Configuration::class, '1.0.0-1d2e978abcf8c719ec065d3cfed2123504c3d31f'),
        array(Router::class, '0.0.1-40652342c192d84a8e49b86c4026a507632c6992'),
        array(BeansBase::class, '1.0.0-0de1927f102f3047cda4d3beb3a127fe00246a5d'),
        array(User::class, '0.0.1-ce5fcd2437f6bc585927d008662de507f9ed9ecf'),
        array(BaseModule::class, '1.0.0-7a22acfc8f82409384d6babd362de111d4ba651b'),
        array(Main::class, '1.0.0-b99a4acc25db0cebcc6b61a76d562f51be03b787'),
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