<?php
declare(strict_types=1);

namespace Unit;

use Unit\AfrClassDependencyExtended as AfrClassDependency;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestClasses/bootstrapTestClasses.php';

class clearClassInfo_AfrClassDependencyTest extends TestCase
{
    static function clearClassInfoProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        AfrClassDependency::flush();

        $aReturn = [];
        $aReturn[] = ['imaginary\un_existent'];
        $aReturn[] = ['GlobalMockInterface'];
        $aReturn[] = ['GlobalMockTrait'];
        $aReturn[] = ['GlobalMockClass2'];
        $aReturn[] = ['GlobalMockSingleton'];

        return $aReturn;
    }

    /**
     * @test
     * @dataProvider clearClassInfoProvider
     */
    public function clearClassInfoTest($sFQCN): void
    {
        AfrClassDependency::getClassInfo($sFQCN);
        $this->assertSame(true, isset(AfrClassDependency::getADependency()[$sFQCN]));
        AfrClassDependency::clearClassInfo($sFQCN);
        $this->assertSame(false, isset(AfrClassDependency::getADependency()[$sFQCN]));
    }


}