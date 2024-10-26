<?php
declare(strict_types=1);

namespace Unit;

use Unit\AfrClassDependencyExtended as AfrClassDependency;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestClasses/bootstrapTestClasses.php';
//AfrClassDependency::setSkipNamespaceInfoGathering(['Autoframe\\Core\\','Symfony\Component\Finder']);
//AfrClassDependency::setSkipClassInfoGathering(['URLify','phpDocumentor\Reflection\DocBlock\Tags\Reference\Fqsen']);




class clearDependencyInfo_AfrClassDependencyTest extends TestCase
{
    static function clearDependencyInfoProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        AfrClassDependency::flush();

        $aReturn = [];
        $aReturn[] = ['imaginary\un_existent'];
        $aReturn[] = ['GlobalMockSingleton'];

        return $aReturn;
    }

    /**
     * @test
     * @dataProvider clearDependencyInfoProvider
     */
    public function clearDependencyInfoTest($sFQCN): void
    {
        AfrClassDependency::getClassInfo($sFQCN);
        $this->assertSame(true, count(AfrClassDependency::getADependency())>0);
        AfrClassDependency::clearDependencyInfo();
        $this->assertSame(0, count(AfrClassDependency::getADependency()));
    }


}