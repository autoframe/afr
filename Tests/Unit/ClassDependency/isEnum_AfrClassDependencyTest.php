<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\ClassDependency\AfrClassDependency;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestClasses/bootstrapTestClasses.php';

class isEnum_AfrClassDependencyTest extends TestCase
{


    static function isEnumProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;

        AfrClassDependency::flush();

        $aDeps = [
            'GlobalMockInterfaceExa' => false,
            'GlobalMockInterfaceExb' => false,
            'GlobalMockInterface' => false,
            'GlobalMockTraitSub' => false,
            'GlobalMockTrait' => false,
            'GlobalMockAbstract' => false,
            'GlobalMockClass' => false,
            'GlobalMockClass2' => false,
            'GlobalMockSingleton' => false,
            __CLASS__ => false,
        ];
        $aDeps['GlobalMockEnum'] = PHP_VERSION_ID >= 81000;

        $aReturn = [];
        foreach ($aDeps as $sClassDep => $bExp) {
            $aReturn[] = [AfrClassDependency::getClassInfo($sClassDep), $bExp];
        }
        return $aReturn;
    }

    /**
     * @test
     * @dataProvider isEnumProvider
     */
    public function isEnumTest(AfrClassDependency $oDep, bool $bExpected): void
    {
        $this->assertSame($bExpected, $oDep->isEnum());
    }


}