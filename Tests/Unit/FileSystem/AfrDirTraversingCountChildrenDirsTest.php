<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\FileSystem\Traversing\AfrDirTraversingCollectionClass;
use Autoframe\Core\FileSystem\Traversing\AfrDirTraversingCountChildrenDirsClass;
use PHPUnit\Framework\TestCase;

class AfrDirTraversingCountChildrenDirsTest extends TestCase
{
    public static function countAllChildrenDirsDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        $d1 = __DIR__;
        $d2 = __DIR__ . DIRECTORY_SEPARATOR . 'ChildrenDirs';
        return [
            [$d1, 3],
            [$d2, 0],
        ];
    }

    /**
     * @test
     * @dataProvider countAllChildrenDirsDataProvider
     */
    public function countAllChildrenDirsTest(string $sPath, int $iExpected): void
    {
        $iFound = AfrDirTraversingCollectionClass::getInstance()->countAllChildrenDirs($sPath);
        $this->assertSame($iExpected, $iFound, print_r($iFound, true));
    }


}