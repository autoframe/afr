<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\ClassDependency\AfrClassDependency;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestClasses/bootstrapTestClasses.php';

class doIDependOn_AfrClassDependencyTest extends TestCase
{
    static function doIDependOnProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;

        AfrClassDependency::flush();
        $aDeps = [
            'GlobalMockInterfaceExa' => [
                'GlobalMockInterfaceExa' => false,
                'GlobalMockInterfaceExb' => false,
                'GlobalMockInterface' => false,
                'GlobalMockTraitSub' => false,
                'GlobalMockTrait' => false,
                'GlobalMockAbstract' => false,
                'GlobalMockClass' => false,
                'GlobalMockClass2' => false,
                'GlobalMockSingleton' => false,
                'imaginary\un_existent' => false,
                'GlobalMockEnum' => false,
            ],
            'GlobalMockInterfaceExb' => [
                'GlobalMockInterfaceExa' => false,
                'GlobalMockInterfaceExb' => false,
                'GlobalMockInterface' => false,
                'GlobalMockTraitSub' => false,
                'GlobalMockTrait' => false,
                'GlobalMockAbstract' => false,
                'GlobalMockClass' => false,
                'GlobalMockClass2' => false,
                'GlobalMockSingleton' => false,
                'imaginary\un_existent' => false,
                'GlobalMockEnum' => false,
            ],
            'GlobalMockInterface' => [
                'GlobalMockInterfaceExa' => true,
                'GlobalMockInterfaceExb' => true,
                'GlobalMockInterface' => false,
                'GlobalMockTraitSub' => false,
                'GlobalMockTrait' => false,
                'GlobalMockAbstract' => false,
                'GlobalMockClass' => false,
                'GlobalMockClass2' => false,
                'GlobalMockSingleton' => false,
                'imaginary\un_existent' => false,
                'GlobalMockEnum' => false,
            ],
            'GlobalMockTraitSub' => [
                'GlobalMockInterfaceExa' => false,
                'GlobalMockInterfaceExb' => false,
                'GlobalMockInterface' => false,
                'GlobalMockTraitSub' => false,
                'GlobalMockTrait' => false,
                'GlobalMockAbstract' => false,
                'GlobalMockClass' => false,
                'GlobalMockClass2' => false,
                'GlobalMockSingleton' => false,
                'imaginary\un_existent' => false,
                'GlobalMockEnum' => false,
            ],
            'GlobalMockTrait' => [
                'GlobalMockInterfaceExa' => false,
                'GlobalMockInterfaceExb' => false,
                'GlobalMockInterface' => false,
                'GlobalMockTraitSub' => true,
                'GlobalMockTrait' => false,
                'GlobalMockAbstract' => false,
                'GlobalMockClass' => false,
                'GlobalMockClass2' => false,
                'GlobalMockSingleton' => false,
                'imaginary\un_existent' => false,
                'GlobalMockEnum' => false,
            ],
            'GlobalMockAbstract' => [
                'GlobalMockInterfaceExa' => true,
                'GlobalMockInterfaceExb' => true,
                'GlobalMockInterface' => true,
                'GlobalMockTraitSub' => true,
                'GlobalMockTrait' => true,
                'GlobalMockAbstract' => false,
                'GlobalMockClass' => false,
                'GlobalMockClass2' => false,
                'GlobalMockSingleton' => false,
                'imaginary\un_existent' => false,
                'GlobalMockEnum' => false,
            ],
            'GlobalMockClass' => [
                'GlobalMockInterfaceExa' => true,
                'GlobalMockInterfaceExb' => true,
                'GlobalMockInterface' => true,
                'GlobalMockTraitSub' => true,
                'GlobalMockTrait' => true,
                'GlobalMockAbstract' => true,
                'GlobalMockClass' => false,
                'GlobalMockClass2' => false,
                'GlobalMockSingleton' => false,
                'imaginary\un_existent' => false,
                'GlobalMockEnum' => false,
            ],
            'GlobalMockClass2' => [
                'GlobalMockInterfaceExa' => true,
                'GlobalMockInterfaceExb' => true,
                'GlobalMockInterface' => true,
                'GlobalMockTraitSub' => true,
                'GlobalMockTrait' => true,
                'GlobalMockAbstract' => true,
                'GlobalMockClass' => true,
                'GlobalMockClass2' => false,
                'GlobalMockSingleton' => false,
                'imaginary\un_existent' => false,
                'GlobalMockEnum' => false,
            ],
            'GlobalMockSingleton' => [
                'GlobalMockInterfaceExa' => true,
                'GlobalMockInterfaceExb' => true,
                'GlobalMockInterface' => true,
                'GlobalMockTraitSub' => true,
                'GlobalMockTrait' => true,
                'GlobalMockAbstract' => true,
                'GlobalMockClass' => true,
                'GlobalMockClass2' => true,
                'GlobalMockSingleton' => false,
                'imaginary\un_existent' => false,
                'GlobalMockEnum' => false,
            ],
            'imaginary\un_existent' => [
                'GlobalMockInterfaceExa' => false,
                'GlobalMockInterfaceExb' => false,
                'GlobalMockInterface' => false,
                'GlobalMockTraitSub' => false,
                'GlobalMockTrait' => false,
                'GlobalMockAbstract' => false,
                'GlobalMockClass' => false,
                'GlobalMockClass2' => false,
                'GlobalMockSingleton' => false,
                'imaginary\un_existent' => false,
                'GlobalMockEnum' => false,
            ],
        ];
        if (PHP_VERSION_ID >= 81000) {
            $aDeps['GlobalMockEnum'] = [
                'GlobalMockInterfaceExa' => false,
                'GlobalMockInterfaceExb' => false,
                'GlobalMockInterface' => false,
                'GlobalMockTraitSub' => false,
                'GlobalMockTrait' => false,
                'GlobalMockAbstract' => false,
                'GlobalMockClass' => false,
                'GlobalMockClass2' => false,
                'GlobalMockSingleton' => false,
                'imaginary\un_existent' => false,
                'GlobalMockEnum' => false,
            ];
        }
        $aReturn = [];
        foreach ($aDeps as $sClassDep => $aMap) {
            $aReturn[] = [AfrClassDependency::getClassInfo($sClassDep), $aMap];
        }
        return $aReturn;
    }

    /**
     * @test
     * @dataProvider doIDependOnProvider
     */
    public function doIDependOnTest(AfrClassDependency $oDep, array $aMap): void
    {
        foreach ($aMap as $sMapClass => $bExpected) {
            $this->assertSame($bExpected, $oDep->doIDependOn($sMapClass));
        }
    }


}