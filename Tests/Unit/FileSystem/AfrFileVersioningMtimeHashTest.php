<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\FileSystem\Versioning\AfrFileVersioningMtimeHashClass;
use Autoframe\Core\FileSystem\Versioning\Exception\AfrFileSystemVersioningException;
use PHPUnit\Framework\TestCase;

class AfrFileVersioningMtimeHashTest extends TestCase
{
    public static function fileVersioningMtimeHashProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        return [
            [__FILE__, false], //file without error
            [__FILE__, true], //can throw error if file is missing, but it does
            ['x', false], //wrong file without error
            ['x', true],//wrong file that throw error
        ];
    }

    /**
     * @test
     * @dataProvider fileVersioningMtimeHashProvider
     */
    public function fileVersioningMtimeHashTest(string $sFile, bool $bCanThrow): void
    {
        $oClass = AfrFileVersioningMtimeHashClass::getInstance();
        if ($bCanThrow) {
            if (!is_file($sFile)) {
                $this->expectException(AfrFileSystemVersioningException::class);
                $sHash = $oClass->fileVersioningMtimeHash($sFile, true);
            } else {
                $sHash = $oClass->fileVersioningMtimeHash($sFile, true);
                $this->assertSame(true, strlen($sHash) > 7);
            }

        } else {
            $sHash = $oClass->fileVersioningMtimeHash($sFile, false);
            if ($sHash === '0') {
                //file was not found and zero was returned as hash without a fatal error
                $this->assertSame(true, true);
            } else {
                $this->assertSame(true, strlen($sHash) > 7);
            }
        }

    }


}