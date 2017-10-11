<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class TestFilesVersioning extends TestCase
{
    private $skeletonIds = [
        array('config.skel.php' => '9b600e866804883011ff24bb373043972bcc565b'),
        array('index.php' => '023fe37d8ad5705c8c63e317ac563d5062107955')
    ];

    public function testSkeletonVersion(): void
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