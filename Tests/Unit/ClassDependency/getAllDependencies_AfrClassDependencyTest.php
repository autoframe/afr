<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\ClassDependency\AfrClassDependency;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestClasses/bootstrapTestClasses.php';

class getAllDependencies_AfrClassDependencyTest extends TestCase
{
    static function getAllDependenciesProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        AfrClassDependency::flush();

        $aDeps = [
            'GlobalMockInterfaceExa',
            'GlobalMockInterfaceExb',
            'GlobalMockInterface',
            'GlobalMockTraitSub',
            'GlobalMockTrait',
            'GlobalMockAbstract',
            'GlobalMockClass',
            'GlobalMockClass2',
        ];


        $aReturn = [];
        foreach ($aDeps as $sClassDep) {
            $aReturn[] = [AfrClassDependency::getClassInfo('GlobalMockSingleton'), $sClassDep, true];
        }
        $aReturn[] = [AfrClassDependency::getClassInfo('GlobalMockSingleton'), 'GlobalMockSingleton', false];
        return $aReturn;
    }

    /**
     * @test
     * @dataProvider getAllDependenciesProvider
     */
    public function getAllDependenciesTest(AfrClassDependency $oDep, string $sClassDep, bool $bIsset): void
    {
        $this->assertSame($bIsset, isset($oDep->getAllDependencies()[$sClassDep]));

    }


}