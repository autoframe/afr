<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\ClassDependency\AfrClassDependency;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestClasses/bootstrapTestClasses.php';


class getDependencyInfo_AfrClassDependencyTest extends TestCase
{
    static function getDependencyInfoProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        AfrClassDependency::flush();


        $aReturn = [];
        $aReturn[] = [AfrClassDependency::getClassInfo('GlobalMockSingleton')];

        return $aReturn;
    }

    /**
     * @test
     * @dataProvider getDependencyInfoProvider
     */
    public function getDependencyInfoTest($oDep): void
    {
        $this->assertSame(true, $oDep instanceof AfrClassDependency);

        foreach (AfrClassDependency::getDependencyInfo() as $sFQCN =>$oAfrClassDependency){
            $this->assertSame(true, $oAfrClassDependency instanceof AfrClassDependency);
        }

    }


}