<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class TestFilesVersioning extends TestCase
{
    private $skeletonIds = [
        array('config.skel.php' =>  'd5d54ef280172c3d1207612aa5833a77461cb26a'),
        array('index.php'       =>  'c7f81407b4015c1f0767003adc9c22d0b84066f3'),
        array('routing.php'     =>  '0ff651177dc7ae7556a03168282452bad301b54d'),
        array('autoloader.php'  =>  '031ef2980afd366eac879024b4babf9c65cd4604'),
        array('rb.php'          =>  '3048a94e643aa655861f8f33b9712e6db1a88411'),
        array('.htaccess'       =>  'b4489358d21fffaf06b4acb7c95b34246244c398'),
    ];

    public function testSkeletonVersion()
    {
        foreach ($this->skeletonIds as $skeletonId) {
            $filename = key($skeletonId);
            $uuid = $skeletonId[key($skeletonId)];
            $this->assertEquals(
                True,
                is_file($filename),
                "The " . $filename . " file is neccessary, but is missing. Please fix it!"
            );
            $generatedUUID = hash('sha1', file_get_contents($filename), false);
            $this->assertEquals(
                $uuid, $generatedUUID,
                "The " . $filename . " file is neccessary, but the UUID is changed to " . $generatedUUID . ".\n" .
                "Please modify the UUID in  the related key-values."
            );
        }
    }
}