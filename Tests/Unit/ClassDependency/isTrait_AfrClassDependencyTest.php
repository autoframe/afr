<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\ClassDependency\AfrClassDependency;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestClasses/bootstrapTestClasses.php';

class isTrait_AfrClassDependencyTest extends TestCase
{


    static function isTraitProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;

        AfrClassDependency::flush();
;
        $aDeps = [
            'GlobalMockInterfaceExa' => false,
            'GlobalMockInterfaceExb' => false,
            'GlobalMockInterface' => false,
            'GlobalMockTraitSub' => true,
            'GlobalMockTrait' => true,
            'GlobalMockAbstract' => false,
            'GlobalMockClass' => false,
            'GlobalMockClass2' => false,
            'GlobalMockSingleton' => false,
            __CLASS__ => false,
        ];
        if (PHP_VERSION_ID >= 81000) {
            $aDeps['GlobalMockEnum'] = false;
        }

        $aReturn = [];
        foreach ($aDeps as $sClassDep => $bExp) {
            $aReturn[] = [AfrClassDependency::getClassInfo($sClassDep), $bExp];
        }
        return $aReturn;
    }

    /**
     * @test
     * @dataProvider isTraitProvider
     */
    public function isTraitTest(AfrClassDependency $oDep, bool $bExpected): void
    {
        $this->assertSame($bExpected, $oDep->isTrait());
    }


}