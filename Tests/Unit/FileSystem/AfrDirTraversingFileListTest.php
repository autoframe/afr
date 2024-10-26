<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\FileSystem\Traversing\AfrDirTraversingFileListClass;
use PHPUnit\Framework\TestCase;

class AfrDirTraversingFileListTest extends TestCase
{
    public static function getDirFileListDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        $d1 = __DIR__ . '/../../../';
        $d2 = is_dir($d1 . 'vendor/composer/') ? $d1 . 'vendor/composer/' : $d1 . '../../../vendor/composer/';
        return [
            [$d1, [], function ($aFiles) {
                return in_array('composer.json', $aFiles);
            }],
            [$d1, ['md'], function ($aFiles) {
                return in_array('README.md', $aFiles);
            }],
            [$d2, ['json', 'php'], function ($aFiles) {
                return in_array('autoload_classmap.php', $aFiles) && in_array('installed.json', $aFiles);
            }],
            [$d2, [''], function ($aFiles) {
                return in_array('LICENSE', $aFiles);
            }],
        ];
    }

    /**
     * @test
     * @dataProvider getDirFileListDataProvider
     */
    public function getDirFileListTest(string $sPath, array $aExtFilter, $Fx): void
    {
        $oClass = AfrDirTraversingFileListClass::getInstance();
        $aFiles = $oClass->getDirFileList($sPath, $aExtFilter);
        $this->assertSame(true, $Fx($aFiles), print_r($aFiles, true));

    }


}