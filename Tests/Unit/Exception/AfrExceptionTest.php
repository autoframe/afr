<?php

namespace Unit\Exception;

use Autoframe\Core\Exception\AfrException;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Throwable;

#[CoversClass(AfrException::class)]
#[UsesClass(AfrException::class)]

class AfrExceptionTest extends TestCase
{
    static function afrExceptionDataProvider(): array
    {
        try {
            throw new AfrException('TestCase');
        } catch (AfrException $oEx) {
            return [
                [($oEx instanceof Exception), true],
                [($oEx instanceof Throwable), true],
                [(strlen((string)$oEx) > 0), true],
                [($oEx->getMessage()), 'TestCase'],
            ];
        }

    }

    /**
     * @test
     * @dataProvider afrExceptionDataProvider
     */
    public function afrException($a, $b): void
    {
        $this->assertSame($a, $b);
    }
}