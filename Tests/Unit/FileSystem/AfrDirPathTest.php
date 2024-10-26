<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\FileSystem\DirPath\AfrDirPathTrait;
use PHPUnit\Framework\TestCase;

class AfrDirPathTest extends TestCase
{
    use AfrDirPathTrait;

    protected function getDirPathDefaultSeparators(): array
    {
        return ['\\', '/'];
    }
    protected function countSlahesPMethod(string $sTestPath): array
    {
        $aOut = [];
        foreach ($this->getDirPathDefaultSeparators() as $sDs) {
            $aOut[$sDs] = substr_count($sTestPath, $sDs);
        }
        return $aOut;
    }

    public static function isDirDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        return [
            ['./', false],
            ['../', false],
            ['../../', false],
            ['../../', false],
            ['../../', false],
            [dirname(__FILE__), false],
            [__FILE__, false],
            ['ZZY', false],
            [dirname(__FILE__) . DIRECTORY_SEPARATOR . __FUNCTION__ . '_testDir', true],
        ];
    }

    /**
     * @test
     * @dataProvider isDirDataProvider
     */
    public function isDirTest(string $sDirPath, bool $bCreate): void
    {
        $bMaked = false;
        $bExpected = is_dir($sDirPath);
        if (!$bExpected && $bCreate) {
            $bMaked = mkdir($sDirPath, 0777, true);
            $bExpected = is_dir($sDirPath);
        }
        $r = $this->isDir($sDirPath);
        $this->assertSame($r, $bExpected, print_r(func_get_args(), true));
        if ($bMaked) {
            rmdir($sDirPath);
        }
    }


    public static function openDirDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        return [
            ['.'],
            ['./'],
            ['../'],
            ['..'],
            ['../../'],
            ['../..'],
            [__DIR__],
            [dirname(__FILE__)],
        ];
    }

    /**
     * @test
     * @dataProvider openDirDataProvider
     */
    public function openDirTest(string $sDirPath): void
    {
        $rDir = $this->openDir($sDirPath);
        $bIsOpened = is_resource($rDir);
        $this->assertSame(
            $bIsOpened,
            $this->isDir($sDirPath),
            'Fail: openDir ' . $sDirPath
        );
        if ($bIsOpened) {
            closedir($rDir);
        }

    }


    public static function detectDirectorySeparatorFromPathDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        return [
            [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR],
            ['/', '/'],
            ['../', '/'],
            ['./', '/'],
            ['..', DIRECTORY_SEPARATOR],
            ['.', DIRECTORY_SEPARATOR],
            ['', DIRECTORY_SEPARATOR],
            ['999', DIRECTORY_SEPARATOR],
            [__DIR__, DIRECTORY_SEPARATOR],
            ['./dsadas/gfdgd/ffff', '/'],
            ['./dsadas/gfdgd\\ffff', '/'],
            ['\\dsadas\\gfdgd\\ffff', '\\'],
            ['\\\\192.168.1.2/gfdgd/ffff', '\\'],
            ['\\\\192.168.1.2\gfdgd\ffff', '\\'],
            ['C:\\Windows/system/fff/ff', '\\'],
            ['C:\\Windows\\system', '\\'],
            ['/Windows\\system', '/'],
            ['/Windows\\system\\', '\\'],
            ['/Windows/system', '/'],
        ];
    }

    /**
     * @test
     * @dataProvider detectDirectorySeparatorFromPathDataProvider
     */
    public function detectDirectorySeparatorFromPathTest(string $sTestPath, string $sExpected): void
    {
        $this->assertSame(
            $this->detectDirectorySeparatorFromPath($sTestPath),
            $sExpected,
            print_r(func_get_args(), true)
        );
    }


    public static function addRemoveUniformPathsDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        return [
            ['/'],
            ['\\'],
            ['../'],
            ['..\\'],
            ['./'],
            ['.\\'],
            ['..'],
            ['.'],
            [''],
            ['999'],
            [__DIR__],
            [__DIR__ . DIRECTORY_SEPARATOR],
            ['./dsadas/gfdgd/ffff'],
            ['\dsadas/gfdgd/ffff'],
            ['./dsadas/gfdgd\\ffff'],
            ['\\dsadas\\gfdgd\\ffff'],
            ['\\\\dsadas\\gfdgd/ffff\\'],
            ['C:\\Windows/system'],
            ['C:\\Windows\\system'],
        ];
    }

    /**
     * @test
     * @dataProvider addRemoveUniformPathsDataProvider
     */
    public function removeFinalSlashTest(string $sTestPath): void
    {
        foreach ($this->getDirPathDefaultSeparators() as $sDs) {
            $this->assertNotEquals(
                $sDs,
                substr($this->removeFinalSlash($sTestPath), -1, 1),
                'Fail: removeFinalSlash ' . $sTestPath
            );
        }
    }


    public static function addFinalSlashDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        $aOut = [];
        foreach (self::addRemoveUniformPathsDataProvider(true) as $sTestPath) {
            $aOut[] = [$sTestPath[0]];
        }
        return $aOut;
    }

    /**
     * @test
     * @dataProvider addFinalSlashDataProvider
     */
    public function addFinalSlashTest($sTestPath): void
    {
        $sExpectedEndingSlash = $this->detectDirectorySeparatorFromPath($sTestPath);
        $sTestPathReturn = $this->addFinalSlash($sTestPath);
        $sLastChr = substr($sTestPathReturn, -1, 1);
        $sLastBut1Chr = strlen($sLastChr) > 1 ? substr($sTestPathReturn, -2, 1) : '';

        $this->assertSame(
            $sExpectedEndingSlash,
            $sLastChr,
            'Fail: dirPathAddFinalSlash ' . $sTestPath . ' : ' . $sTestPathReturn
        );
        if (strlen($sLastBut1Chr) > 0) {
            foreach ($this->getDirPathDefaultSeparators() as $sBaseDs) {
                $this->assertNotEquals(
                    $sBaseDs,
                    $sLastBut1Chr,
                    'Fail: dirPathAddFinalSlash ' . $sTestPath . ' : ' . $sTestPathReturn
                );
            }
        }


    }



    /**
     * @test
     * @dataProvider addRemoveUniformPathsDataProvider
     */
    public function makeUniformSlashStyleTest(string $sTestPath): void
    {
        $sDs = $this->detectDirectorySeparatorFromPath($sTestPath);
        $sTestPathNew = $this->makeUniformSlashStyle($sTestPath);
        $aErr = [
            '$sTestPath' => $sTestPath,
            '$sDs' => $sDs,
            '$sTestPathNew' => $sTestPathNew,
        ];

        $aBefore = $this->countSlahesPMethod($sTestPath);
        $aAfter = $this->countSlahesPMethod($sTestPathNew);
        $iTotalBefore = array_sum($aBefore);
        $iTotalAfter = array_sum($aAfter);

        $aErr['$aBefore'] = $aBefore;
        $aErr['$iTotalBefore'] = $iTotalBefore;
        $aErr['$aAfter'] = $aAfter;
        $aErr['$iTotalAfter'] = $iTotalAfter;


        $this->assertSame(
            $iTotalBefore,
            $iTotalAfter,
            print_r($aErr, true)
        );
        $this->assertSame(
            $iTotalBefore,
            $aAfter[$sDs],
            print_r($aErr, true)
        );
        $this->assertSame(
            0,
            array_sum($aAfter) - $aAfter[$sDs],
            print_r($aErr, true)
        );

        if ($iTotalBefore > 0) { //slashes are found
            $iCountFoundInAfter = 0;
            foreach ($aAfter as $iCountDs) {
                if ($iCountDs > 0) {
                    if ($iCountFoundInAfter) {
                        $this->assertSame(
                            $iTotalBefore,
                            $iCountDs,
                            print_r($aErr, true)
                        );
                    }
                    $iCountFoundInAfter += $iCountDs; //found only once after detection
                }
            }
        }

    }




    public static function correctDirPathFormatTestDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        $set1 = './dsadas\\gfdgd/ffff';
        $set2 = 'C:\\Windows/system';
        $set3 = __DIR__;

        //$sExpected,  $sDirPathData,  $bWithFinalSlash = true, $bCorrectSlashStyle = true
        return [
            //0 $sExpected, $sDirPathData, $bWithFinalSlash, $bCorrectSlashStyle
            [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, true, true],
            ['', DIRECTORY_SEPARATOR, false, true],
            ['', DIRECTORY_SEPARATOR, false, false],
            ['/', '/', true, true],
            ['\\', '\\', true, true],

            //5  $sExpected, $sDirPathData, $bWithFinalSlash, $bCorrectSlashStyle
            [str_replace('\\', '/', $set1) . '/', $set1, true, true, '/'],
            [str_replace('/', '\\', $set1), $set1.'\\\\', false, true],
            [$set1, $set1 . '/', false, false],

            //$set2 = 'C:\\Windows/system';
            //8 $sExpected, $sDirPathData, $bWithFinalSlash, $bCorrectSlashStyle
            [str_replace('/', '\\', $set2) . '\\', $set2, true, true, '\\'],
            [$set2 . '\\', $set2, true, false, '/'],
            [str_replace('/', '\\', $set2).'\\', $set2, true, true],
            [str_replace('/', '\\', $set2), $set2, false, true],
            [$set2, $set2 . '/', false, false],

            //13 $sExpected, $sDirPathData, $bWithFinalSlash, $bCorrectSlashStyle
            [$set3 . DIRECTORY_SEPARATOR, $set3, true, true],
            [$set3 . DIRECTORY_SEPARATOR, $set3 . '\\', true, false],
            [$set3 . DIRECTORY_SEPARATOR, $set3 . '/', true, false],
            [str_replace('/', DIRECTORY_SEPARATOR, $set3) . DIRECTORY_SEPARATOR, $set3, true, true],
            [str_replace('/', DIRECTORY_SEPARATOR, $set3), $set3, false, true],
            [$set3, $set3 . '/', false, false],
        ];
    }


    /**
     * @test
     * @dataProvider correctDirPathFormatTestDataProvider
     */
    public function correctDirPathFormatTest(
        string $sExpected,
        string $sDirPathData,
        bool   $bWithFinalSlash = true,
        bool   $bCorrectSlashStyle = true
    ): void
    {
        $this->assertSame(
            $sExpected,
            $this->correctDirPathFormat($sDirPathData, $bWithFinalSlash, $bCorrectSlashStyle),
            'Fail: dirPathCorrectFormat ' . $sDirPathData . ' : ' . $sExpected . "\nParams:" . implode(';', func_get_args())
        );
    }




    public static function simplifyAbsolutePathDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        return [
            ['this/is/../a/./test/.///is','this/a/test/is'],
            ['./l1/l2/../','l1'],
            ['x/../l1/l2/../','l1'],
            ['../l1/l2/../','../l1'],
            ['../l1/l2/../..','..'],
            ['../l1/l2/../../','..'],
        ];
    }
    /**
     * @test
     * @dataProvider simplifyAbsolutePathDataProvider
     */
    public function simplifyAbsolutePathTest(
        string $sDirPath,
        string $sExpected
    ): void
    {
        $this->assertSame(
            $sExpected,
            $this->simplifyAbsolutePath($sDirPath),
            'Fail: simplifyAbsolutePath ' . $sDirPath . ' : ' . $sExpected . "\nParams:" . implode(';', func_get_args())
        );
    }


}