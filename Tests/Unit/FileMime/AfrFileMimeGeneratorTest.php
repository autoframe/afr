<?php

namespace Unit\FileMime;

use Autoframe\Core\FileMime\Exception\AfrFileSystemMimeException;
use Autoframe\Core\FileMime\AfrFileMimeGeneratorClass;
use PHPUnit\Framework\TestCase;


class AfrFileMimeGeneratorTest extends TestCase
{
    protected AfrFileMimeGeneratorClass $oGenerator;

    protected function setUp(): void
    {
        $this->oGenerator = AfrFileMimeGeneratorClass::getInstance();
    }

    public static function synchronizeMimeTypesFromApacheDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;

        return [
            [3600 * 24 * 365 * 2, 1], //mime.types file is at least 2 years new
            [0, 1], //renew  mime.types file and regenerate traits
            [3600 * 24 * 365 * 2, 1] //mime.types file is at least 2 years new
        ];
    }


    /**
     * @test
     * @dataProvider synchronizeMimeTypesFromApacheDataProvider
     */
    public function synchronizeMimeTypesFromApacheTest($iMaxAgeSeconds, $iRegenerateTraitsIfxSecondsOlderThanMimes): void
    {
        //THIS TEST RUNS ONLY ON LOCAL DEV BECAUSE IT MAKES EXTERNAL HTTP REQUESTS!
        $bInsideProductionVendorDir = strpos(__DIR__, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false;
        if ($bInsideProductionVendorDir || rand(1, 50) != 7) { //2% chance to run on local
            $this->assertSame(1, 1);
            return;
        }

        try {
            $bResponse = $this->oGenerator->synchronizeMimeTypesFromApache(
                $iMaxAgeSeconds,
                $iRegenerateTraitsIfxSecondsOlderThanMimes
            );
            $this->assertSame(
                true,
                $bResponse,
                'Timeout or parse error on https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types'
            );
            $this->assertSame(
                true,
                $this->oGenerator->traitsAreUpToDate($iRegenerateTraitsIfxSecondsOlderThanMimes),
                'Traits are not up to date!'
            );

        } catch (AfrFileSystemMimeException $e) {
            $this->assertSame(
                true,
                false,
                $e->getMessage() . ";\n" . $e->getTraceAsString()
            );
        }

    }

}
