<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\ClassDependency\AfrClassDependency;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestClasses/bootstrapTestClasses.php';

class getType_AfrClassDependencyTest extends TestCase
{
    static function getTypeProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;

        AfrClassDependency::flush();
        AfrClassDependency::setSkipClassInfo(['GlobalMockClass2']);
        $aReturn = [];
        $aReturn[] = [AfrClassDependency::getClassInfo('GlobalMockSingleton'), 'class'];
        $aReturn[] = [AfrClassDependency::getClassInfo('GlobalMockClass2'), 'skip'];
        $aReturn[] = [AfrClassDependency::getClassInfo('GlobalMockClass'), 'class'];
        $aReturn[] = [AfrClassDependency::getClassInfo('GlobalMockAbstract'), 'class'];
        $aReturn[] = [AfrClassDependency::getClassInfo('GlobalMockTrait'), 'trait'];
        $aReturn[] = [AfrClassDependency::getClassInfo('GlobalMockTraitSub'), 'trait'];
        $aReturn[] = [AfrClassDependency::getClassInfo('GlobalMockInterface'), 'interface'];
        $aReturn[] = [AfrClassDependency::getClassInfo('GlobalMockInterfaceExb'), 'interface'];
        $aReturn[] = [AfrClassDependency::getClassInfo('GlobalMockInterfaceExa'), 'interface'];
        if (PHP_VERSION_ID >= 81000) {
            $aReturn[] = [AfrClassDependency::getClassInfo('GlobalMockEnum'), 'enum'];
        } else {
            $aReturn[] = [AfrClassDependency::getClassInfo('GlobalMockEnum'), 'unknown'];
        }
        $aReturn[] = [AfrClassDependency::getClassInfo('Global\\MockSi\\ngleton'), 'unknown'];

        return $aReturn;
    }

    /**
     * @test
     * @dataProvider getTypeProvider
     */
    public function getTypeTest(AfrClassDependency $oDep, string $sType): void
    {
        $this->assertSame($sType, $oDep->getType());
    }


}