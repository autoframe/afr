<?php
declare(strict_types=1);

namespace Unit;

use Unit\AfrClassDependencyExtended as AfrClassDependency;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestClasses/bootstrapTestClasses.php';


class getDebugFatalError_clearDebugFatalError_AfrClassDependencyTest extends TestCase
{
    static function getClearDebugFatalErrorProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        AfrClassDependency::flush();


        $aReturn = [];
        $aReturn[] = [AfrClassDependency::getClassInfo(new \stdClass())];

        return $aReturn;
    }

    /**
     * @test
     * @dataProvider getClearDebugFatalErrorProvider
     */
    public function getClearDebugFatalErrorTest($oDep): void
    {
        $mErrArr = AfrClassDependency::getDebugFatalError();
        $this->assertSame('array', gettype($mErrArr));
        $this->assertCount(0, $mErrArr);

        AfrClassDependency::setAFatalErr('CorruptedClass');
        $mErrArr = AfrClassDependency::getDebugFatalError();
        $this->assertSame('array', gettype($mErrArr));
        $this->assertCount(1, $mErrArr);


        AfrClassDependency::clearDebugFatalError();
        $mErrArr = AfrClassDependency::getDebugFatalError();
        $this->assertSame('array', gettype($mErrArr));
        $this->assertCount(0, $mErrArr);
    }


}