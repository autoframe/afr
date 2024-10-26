<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\FileSystem\Versioning\AfrDirMaxFileMtimeClass;
use PHPUnit\Framework\TestCase;

class AfrDirMaxFileMtimeTest extends TestCase
{
    public static function getDirMaxFileMtimeProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        return [
            [
                __DIR__ . '/../../',
                strtotime('2023-05-01 00:00:00'),
                4, //$iMaxSubDirs
                [],//extension
                [] //skip
            ],
            [
                [
                    __DIR__,
                    __DIR__ . '/../../',
                ],
                strtotime('2023-05-01 00:00:00'),
                2,
                [],//extension
                [] //skip

            ],
            [
                __DIR__,
                strtotime('2023-05-31 00:00:00'),
                1,
                ['txt'],//extension
                [] //skip

            ],
        ];


    }

    /**
     * @test
     * @dataProvider getDirMaxFileMtimeProvider
     */
    public function getDirMaxFileMtimeTest($mDirPath, int $iLibWriteTime, int $iMaxSubDirs, array $aExtensions, array $aSkip): void
    {
        $oClass = AfrDirMaxFileMtimeClass::getInstance();
        if (empty($aExtensions)) {
            $iMaxTs = $oClass->getDirMaxFileMtime($mDirPath, $iMaxSubDirs, false, (bool)rand(0, 1), $aExtensions, $aSkip);
            $this->assertGreaterThan($iLibWriteTime, $iMaxTs, date('Y-m-d H:i:s', $iLibWriteTime) . ' GTE? ' . date('Y-m-d H:i:s', $iMaxTs));
        } else { //extension tests
            $sWriteDir = $mDirPath;
            if (is_array($mDirPath)) {
                $sWriteDir = $mDirPath[0];
            }
            $sWriteFile = $sWriteDir . DIRECTORY_SEPARATOR . 'timestamp.' . ltrim(strtolower($aExtensions[0]), '.');
            file_put_contents($sWriteFile, time());
            usleep(10 * 1000);
            $iMaxTs = $oClass->getDirMaxFileMtime($mDirPath, $iMaxSubDirs, false, (bool)rand(0, 1), $aExtensions, $aSkip);
            $this->assertGreaterThan(time() - 5, $iMaxTs);
            @unlink($sWriteFile);
        }

    }


}