<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\ClassDependency\AfrClassDependency;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestClasses/bootstrapTestClasses.php';

class getClassName_AfrClassDependencyTest extends TestCase
{
    static function getClassNameProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        AfrClassDependency::flush();


        $aDeps = [
            'GlobalMockInterfaceExa',
            'GlobalMockTraitSub',
            'GlobalMockAbstract',
            'GlobalMockClass',
            'GlobalMockSingleton',
            __CLASS__,
        ];
        if (PHP_VERSION_ID >= 81000) {
            $aDeps[] = 'GlobalMockEnum';
        }


        $aReturn = [];
        foreach ($aDeps as $sClassDep) {
            $aReturn[] = [AfrClassDependency::getClassInfo($sClassDep), $sClassDep];
        }
        return $aReturn;
    }

    /**
     * @test
     * @dataProvider getClassNameProvider
     */
    public function getClassNameTest(AfrClassDependency $oDep, string $sClassDep): void
    {
        $this->assertSame($sClassDep, $oDep->getClassName());
        $this->assertSame($sClassDep, (string)$oDep);

    }


}