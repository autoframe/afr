<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\ClassDependency\AfrClassDependency;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestClasses/bootstrapTestClasses.php';

class setSkipClassInfo_AfrClassDependencyTest extends TestCase
{
    static function setSkipClassInfoProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        $aReturn = [];
        $aReturn[] = ['imaginary\un_existent'];
        $aReturn[] = ['GlobalMockSingleton'];
        $aReturn[] = ['PHPUnit\Framework\TestSuite'];
        return $aReturn;
    }

    /**
     * @test
     * @dataProvider setSkipClassInfoProvider
     */
    public function setSkipClassInfoTest(string $sFQCN): void
    {
        AfrClassDependency::flush();
        $this->assertSame(true,AfrClassDependency::getClassInfo($sFQCN)->getType() !== 'skip');

        AfrClassDependency::setSkipClassInfo([$sFQCN]);
        $this->assertSame(true, in_array(
            AfrClassDependency::getClassInfo($sFQCN)->getType(),
            ['skip', 'unknown']
        ));
    }




    static function setSkipClassInfoMergeProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        $aFQCN = ['imaginary\un_existent','GlobalMockSingleton','PHPUnit\Framework\TestSuite'];
        shuffle($aFQCN);
        return [[$aFQCN]];
    }

    /**
     * @test
     * @dataProvider setSkipClassInfoMergeProvider
     */
    public function setSkipClassInfoMergeFalseTest(array $aFQCN): void
    {
        AfrClassDependency::flush();
        foreach($aFQCN as $sFQCN){
            $this->assertSame(true,AfrClassDependency::getClassInfo($sFQCN)->getType() !== 'skip');
            AfrClassDependency::setSkipClassInfo([$sFQCN],false);
            $this->assertSame(true, in_array(
                AfrClassDependency::getClassInfo($sFQCN)->getType(),
                ['skip', 'unknown']
            ));
            foreach(array_diff($aFQCN,[$sFQCN]) as $sClassDiff){
                $this->assertSame(true,AfrClassDependency::getClassInfo($sClassDiff)->getType() !== 'skip');
            }
        }
    }


    /**
     * @test
     * @dataProvider setSkipClassInfoMergeProvider
     */
    public function setSkipClassInfoMergTrueTest(array $aFQCN): void
    {
        AfrClassDependency::flush();
        $aSkipped = [];
        foreach($aFQCN as $sFQCN){
            $this->assertSame(true,AfrClassDependency::getClassInfo($sFQCN)->getType() !== 'skip');
            AfrClassDependency::setSkipClassInfo([$sFQCN],true);
            $aSkipped[] = $sFQCN;
            $this->assertSame(true, in_array(
                AfrClassDependency::getClassInfo($sFQCN)->getType(),
                ['skip', 'unknown']
            ));
            foreach(array_diff($aFQCN,$aSkipped) as $sClassDiff){
                $this->assertSame(true,AfrClassDependency::getClassInfo($sClassDiff)->getType() !== 'skip');
            }
        }
    }



    /**
     * @test
     */
    public function setSkipClassInfoDuplicatesTest(): void
    {
        $aDuplicates = ['GlobalMockSingleton','PHPUnit\Framework\TestSuite','GlobalMockSingleton'];
        $aExpected = ['GlobalMockSingleton','PHPUnit\Framework\TestSuite'];
        sort($aExpected);
        foreach($aDuplicates as $sFQCN){
            foreach ([true,false] as $bMergeWithExisting){
                AfrClassDependency::flush();
                $this->assertSame(true,AfrClassDependency::getClassInfo($sFQCN)->getType() !== 'skip');
                AfrClassDependency::setSkipClassInfo($aDuplicates,$bMergeWithExisting);
                $this->assertSame(true, in_array(
                    AfrClassDependency::getClassInfo($sFQCN)->getType(),
                    ['skip', 'unknown']
                ));
                $aProcessed = AfrClassDependency::getSkipClassInfo();
                sort($aProcessed);
                $this->assertSame(2, count($aProcessed));
                $this->assertSame(
                    $aExpected,
                    $aProcessed,
                    print_r([$aExpected,$aProcessed],true)
                );
            }
        }
    }

}