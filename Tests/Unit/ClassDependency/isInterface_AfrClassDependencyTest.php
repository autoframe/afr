<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\ClassDependency\AfrClassDependency;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestClasses/bootstrapTestClasses.php';

class isInterface_AfrClassDependencyTest extends TestCase
{


    static function isInterfaceProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;

        AfrClassDependency::flush();

        $aDeps = [
            'GlobalMockInterfaceExa' => true,
            'GlobalMockInterfaceExb' => true,
            'GlobalMockInterface' => true,
            'GlobalMockTraitSub' => false,
            'GlobalMockTrait' => false,
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
     * @dataProvider isInterfaceProvider
     */
    public function isInterfaceTest(AfrClassDependency $oDep, bool $bExpected): void
    {
        $this->assertSame($bExpected, $oDep->isInterface());
    }


}