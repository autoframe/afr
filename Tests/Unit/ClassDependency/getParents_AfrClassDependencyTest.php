<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\ClassDependency\AfrClassDependency;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestClasses/bootstrapTestClasses.php';

class getParents_AfrClassDependencyTest extends TestCase
{
    private static function flipT(array $a): array
    {
        $a = array_flip($a);
        foreach ($a as &$v) {
            $v = true;
        }
        return $a;
    }

    static function getParentsProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;

        AfrClassDependency::flush();

        $aDeps = [
            'GlobalMockInterfaceExa' => self::flipT([]),
            'GlobalMockInterfaceExb' => self::flipT([]),
            'GlobalMockInterface' => self::flipT([]),
            'GlobalMockTraitSub' => self::flipT([]),
            'GlobalMockTrait' => self::flipT([]),
            'GlobalMockAbstract' => self::flipT([]),
            'GlobalMockClass' => self::flipT(['GlobalMockAbstract']),
            'GlobalMockClass2' => self::flipT(['GlobalMockAbstract', 'GlobalMockClass']),
            'GlobalMockSingleton' => self::flipT(['GlobalMockAbstract', 'GlobalMockClass', 'GlobalMockClass2']),
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
     * @dataProvider getParentsProvider
     */
    public function getParentsTest(AfrClassDependency $oDep, array $aExpected): void
    {
        $aDetected = $oDep->getParents();
        ksort($aDetected);
        ksort($aExpected);
        $this->assertSame($aExpected, $aDetected);
    }


}