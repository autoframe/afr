<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\ClassDependency\AfrClassDependency;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestClasses/bootstrapTestClasses.php';

class getInterfaces_AfrClassDependencyTest extends TestCase
{
    private static function flipT(array $a): array
    {
        $a = array_flip($a);
        foreach ($a as &$v) {
            $v = true;
        }
        return $a;
    }

    static function getInterfacesProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;

        AfrClassDependency::flush();

        $allInterfaces = self::flipT(['GlobalMockInterfaceExa', 'GlobalMockInterfaceExb','GlobalMockInterface']);
        $aDeps = [
            'GlobalMockInterfaceExa' => self::flipT([]),
            'GlobalMockInterfaceExb' => self::flipT([]),
            'GlobalMockInterface' => self::flipT(['GlobalMockInterfaceExa', 'GlobalMockInterfaceExb']),
            'GlobalMockTraitSub' => self::flipT([]),
            'GlobalMockTrait' => self::flipT([]),
            'GlobalMockAbstract' => $allInterfaces,
            'GlobalMockClass' => $allInterfaces,
            'GlobalMockClass2' => $allInterfaces,
            'GlobalMockSingleton' => $allInterfaces,
        ];
        if (PHP_VERSION_ID >= 81000) {
            $aDeps['GlobalMockEnum'] = self::flipT([]);
        }

        $aReturn = [];
        foreach ($aDeps as $sClassDep => $aVals) {
            $aReturn[] = [AfrClassDependency::getClassInfo($sClassDep), $aVals];
        }
        return $aReturn;
    }

    /**
     * @test
     * @dataProvider getInterfacesProvider
     */
    public function getInterfacesTest(AfrClassDependency $oDep, array $aExpected): void
    {
        $aDetected = $oDep->getInterfaces();
        ksort($aDetected);
        ksort($aExpected);
        $this->assertSame($aExpected, $aDetected);
    }


}