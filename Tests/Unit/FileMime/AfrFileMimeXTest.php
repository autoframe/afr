<?php

namespace Unit\FileMime;

use Autoframe\Core\FileMime\AfrFileMimeClass;
use PHPUnit\Framework\TestCase;

class AfrFileMimeXTest extends TestCase
{

    protected AfrFileMimeClass $oAfrFileMimeClass;

    protected function setUp(): void
    {
        $this->oAfrFileMimeClass = AfrFileMimeClass::getInstance();
    }

    protected function tearDown(): void
    {

    }

    public static function getMimeFromFileNameDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        $sFallback = 'application/octet-stream';
        $aOut = [
            ['./', $sFallback],
            ['../', $sFallback],
            ['../../', $sFallback],
            ['../../', $sFallback],
            ['../../', $sFallback],
            [dirname(__FILE__), $sFallback],
            [DIRECTORY_SEPARATOR, $sFallback],
            ['/', $sFallback],
            ['..', $sFallback],
            ['.', $sFallback],
            ['', $sFallback],
            ['999', $sFallback],
            [__FILE__, 'application/x-httpd-php'],
            ['test.doc', 'application/msword'],
            ['test.jpg', 'image/jpeg'],
            ['test.jpeg', 'image/jpeg'],
            ['test.gif', 'image/gif'],
            ['test.png', 'image/png'],
            ['test.svg', 'image/svg+xml'],
            ['test.mmr', 'image/vnd.fujixerox.edmics-mmr'],
        ];
        foreach (AfrFileMimeClass::$aAfrFileMimeExtensions as $sExt => $sMime) {
            $aOut[] = ['test.' . ucwords($sExt), $sMime];
        }
        return $aOut;
    }

    /**
     * @test
     * @dataProvider getMimeFromFileNameDataProvider
     */
    public function getMimeFromFileNameTest(string $sPath, string $sMime): void
    {
        $this->assertSame($sMime, $this->oAfrFileMimeClass->getMimeFromFileName($sPath));
    }


    public static function getExtensionsForMimeDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        return [
            ['image/jpeg', 'jpe|jpeg|jpg'],
        ];
    }

    /**
     * @test
     * @dataProvider getExtensionsForMimeDataProvider
     */
    public function getExtensionsForMimeTest(string $sMime, $sExpected): void
    {
        $a = $this->oAfrFileMimeClass->getExtensionsForMime($sMime);
        sort($a);
        $this->assertSame(
            implode('|', $a),
            $sExpected,
            print_r(func_get_args(), true)
        );
    }


    public static function getExtensionFromPathDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        return [
            ['/dir/test.jpg', 'jpg'],
            ['/dir', ''],
            ['/dir/', ''],
            ['/dir/README', ''],
        ];
    }


    /**
     * @test
     * @dataProvider getExtensionFromPathDataProvider
     */
    public function getExtensionFromPathTest(string $sPath, $sExpected): void
    {
        $this->assertSame(
            $this->oAfrFileMimeClass->getExtensionFromPath($sPath),
            $sExpected,
            print_r(func_get_args(), true)
        );
    }

    public static function getAllMimesFromFileNameDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        return [
            ['test.wmz', 'application/x-ms-wmz|application/x-msmetafile'],
        ];
    }

    /**
     * @test
     * @dataProvider getAllMimesFromFileNameDataProvider
     */
    public function getAllMimesFromFileNameTest(string $sExt, $sExpected): void
    {
        $a = $this->oAfrFileMimeClass->getAllMimesFromFileName($sExt);
        sort($a);
        $this->assertSame(
            implode('|', $a),
            $sExpected,
            print_r(func_get_args(), true)
        );
    }


}