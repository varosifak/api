<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use MicroLight\Components as Components;

class TestClassVersioning extends TestCase
{
    /**
     * The first element of the array is the class,
     * the second element, the generated UUID.
     * If the version number of class is changed, require
     * renew the UUID.
     */
    private $classes = [
        array(Components\JSON::class, '1.0.0-f4ffdf8390ebb1c203b389906b45a1e28931df67'),
        array(Components\Configuration::class, '1.0.0-1d2e978abcf8c719ec065d3cfed2123504c3d31f'),
    ];

    public function testClassVersion(): void
    {
        foreach ($this->classes as $classInformations) {
            $generatedUUID = $classInformations[0]::$version . "-" . hash(
                    'sha1', $classInformations[0] . $classInformations[0]::$version, false
                );
            $this->assertEquals(
                $classInformations[1], $generatedUUID,
                'The version number of the class has been inconsistent.' .
                'Please update the UUID of the ' . $classInformations[0] . ' class in the' .
                'tests/TestClassVersioning.php file to ' . $generatedUUID
            );
        }
    }

}