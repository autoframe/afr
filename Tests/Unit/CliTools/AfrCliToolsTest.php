<?php
declare(strict_types=1);

namespace Unit\CliTools;

use Autoframe\Core\CliTools\AfrCliDetect;
use Autoframe\Core\CliTools\AfrCliTextColors;
use Autoframe\Core\CliTools\AfrInsideVendor;
use PHPUnit\Framework\TestCase;

class AfrCliToolsTest extends TestCase
{

    /**
     * @test
     */
    public function isCli(): void
    {
        $this->assertSame(AfrCliDetect::isCli(), true);
        $this->assertSame(AfrCliDetect::isWeb(), false);
    }

    /**
     * @test
     */
    public function AfrCliTextColors(): void
    {
        AfrCliTextColors::demo();
        $this->assertSame(true, true);
    }

    /**
     * @test
     */
    public function AfrInsideVendor(): void
    {
        $this->assertSame(true, is_bool(AfrInsideVendor::pathIsInsideVendorDir(__DIR__)));
    }

}