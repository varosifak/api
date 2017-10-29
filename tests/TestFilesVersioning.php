<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class TestFilesVersioning extends TestCase
{
    private $skeletonIds = [
        array('config.skel.php' => '19681aa53632cf534f035e6eedc27d63299ac658'),
        array('index.php' => '7983b188fe50f816c9afe05f7373bf227faf947f')
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