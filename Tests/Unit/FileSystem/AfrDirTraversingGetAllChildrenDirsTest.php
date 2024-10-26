<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\FileSystem\Traversing\AfrDirTraversingGetAllChildrenDirsClass;
use PHPUnit\Framework\TestCase;

class AfrDirTraversingGetAllChildrenDirsTest extends TestCase
{
    public static function AfrDirTraversingGetAllChildrenDirsDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;

        $dTopX = __DIR__ . '/../../../';
        $dTop = is_dir($dTopX . 'vendor/composer/') ? $dTopX  : $dTopX . '../../../';
        $dVendor = $dTop . 'vendor/';

        return [
            [$dTop, 0, false, function ($aFiles) {
                return $aFiles === false;
            }],
            [$dTop, 2, true, function ($aFiles) {
                return
                    isset($aFiles['vendor']) &&
                    !isset($aFiles['.']) &&
                    !isset($aFiles['..']);
            }],
            [$dVendor, 3, true, function ($aFiles) {
                foreach ($aFiles as $lok => $l1) {
                    foreach ($l1 as $l1k => $l2) {
                        foreach ($l2 as $l2k => $l3) {
                            return $l3 === false;
                        }
                    }
                }
                return false;
            }],

        ];
    }

    /**
     * @test
     * @dataProvider AfrDirTraversingGetAllChildrenDirsDataProvider
     */
    public function AfrDirTraversingGetAllChildrenDirsTest(string $sPath, int $iMaxLevels, bool $bFollowSymlinks, $Fx): void
    {
        $oClass = AfrDirTraversingGetAllChildrenDirsClass::getInstance();
        $aFiles = $oClass->getAllChildrenDirs($sPath, $iMaxLevels, $bFollowSymlinks);
        $this->assertSame(true, $Fx($aFiles), print_r($aFiles, true));

    }


}