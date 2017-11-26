<?php
declare(strict_types=1);
include_once(__DIR__ . "/autoload.php");

use PHPUnit\Framework\TestCase;

class TestClassVersioning extends TestCase
{
    /**
     * The first element of the array is the class,
     * the second element, the generated UUID.
     * If the version number of class is changed, require
     * renew the UUID.
     */
    private $classes = [
        array(JSON::class, '1.0.1-3fc96e0f9a9d468d3867a80cc79dfaae97cf701e'),
        array(Configuration::class, '1.0.1-e8af6b9b515fcebbb3e0e060cd21f1d5240df0d6'),
        array(Router::class, '1.0.0-f81ac59902c717a630f3ed9abc4bb56440affc37'),
        array(BaseModule::class, '1.0.1-cbcdb96774c252e3a76dac89e6abb1b22e32221d'),
        array(Main::class, '1.0.1-36a70a1dda7882ff5ea6044c11f2682bebdd59d1'),
        array(Authentication::class, '1.0.0-86980779726665ce08b266a0fab64e734622a673'),
        array(FaFajok::class, '1.0.0-3bffcc283c64a1b3d16957a0154f5ec2d279300d'),
        array(FaKategoriak::class, '1.0.0-435ae4a0e683e18ccc41e7bb84051f6cacbd5590'),
        array(FaRogzitesek::class, '1.0.0-731b7cf1dac69b004bdcb68749938ceed93fb1b6'),
        array(Felevek::class, '1.0.0-f6599320727a3c252dd1bac639c22f645e457e9c'),
        array(Users::class, '1.0.0-de5b8047a5dca612e775a09a8f63d8f4d3812a24'),
        array(UsersUtcak::class, '1.0.0-7aaff51388dbfcd53035e4147f8fdecc636fedc9'),
        array(Utcak::class, '1.0.0-3d2cfd9bbe1d669e84d24ffed5b53933aab264d9'),
        array(Version::class, '1.0.0-a30ff96529b435ce9ee3b9959855b92b01af8cd2'),
        array(AuthModel::class, '1.0.0-79772faca14bac516fc0ed7cdf18c2a9212d70e4'),
    ];

    public function testClassVersions()
    {
        foreach ($this->classes as $classInformations) {
            $generatedUUID = $classInformations[0]::$version . "-" . hash(
                    'sha1', $classInformations[0] . $classInformations[0]::$version, false
                );

            $this->assertEquals(
                $classInformations[1], $generatedUUID,
                $classInformations[0] . ' version error: The version number ' .
                'of the class has been inconsistent. ' .
                'Please update the UUID of the ' . $classInformations[0] . ' class in the ' .
                'tests/TestClassVersioning.php file to ' . $generatedUUID
            );
        }
    }

}