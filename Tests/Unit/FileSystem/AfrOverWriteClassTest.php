<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\FileSystem\OverWrite\AfrOverWriteClass;
use PHPUnit\Framework\TestCase;

class AfrOverWriteClassTest extends TestCase
{
    public static function insideProductionVendorDir(): bool
    {
        return strpos(__DIR__, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false;
    }

    public static function overWriteFileDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        $aTests = [
            ['OVERWRITTEN!' . microtime(true) . rand(1001, 3000), 5, 500, 7.2],
            ['OVERWRITTEN!' . microtime(true) . rand(30001, 60000), 30, 500, 14.1],
            ['OVERWRITTEN!', 160, 500, 15],
        ];
        if (self::insideProductionVendorDir()) {
            $aTests[1] = $aTests[2];
            unset($aTests[2]);
        }
        return $aTests;
    }

    /**
     * @test
     * @dataProvider overWriteFileDataProvider
     */
    public function overWriteFileTest(
        string $sData,
        int    $iTimeoutBeforeStart,
        int    $iMaxRetryMs,
        float  $fDeltaSleepMs): void
    {
        $iUsleepAfter = 4000;
        //local project simulate file lock. this will not run in production or from composer vendor dir
        if (!self::insideProductionVendorDir()) {
            $iUsleepAfter = 10 * 1000;
            //TODO macOS variant
            $execFileArgs = __DIR__ . DIRECTORY_SEPARATOR . 'AfrOverWrite.php ' . $iTimeoutBeforeStart . ' Arg2';
            if (DIRECTORY_SEPARATOR === '\\') { //windows
                $php = (substr(php_ini_loaded_file(), 0, -3) . 'exe');
                pclose(popen("start /B $php $execFileArgs", 'r'));
            } else { //unix
                exec('php ' . $execFileArgs . ' > /dev/null &');
            }
            //sleep 40 ms and wait for the file to be created with other data
            usleep(min($iTimeoutBeforeStart, 150) * 1000);
        }

        $sOverwritePath = __DIR__ . DIRECTORY_SEPARATOR . 'AfrOverWriteClass.txt';
        $this->assertNotEquals($sData, file_get_contents($sOverwritePath));

        $oClass = AfrOverWriteClass::getInstance();
        $bResponse = $oClass->overWriteFile($sOverwritePath, $sData, $iMaxRetryMs, $fDeltaSleepMs);
        $this->assertSame(true, $bResponse);
        $this->assertSame($sData, file_get_contents($sOverwritePath), 'Failed to check that ' . $sOverwritePath . ' contains the data: ' . $sData);
        usleep($iUsleepAfter);
        //@unlink($sOverwritePath);
    }


}