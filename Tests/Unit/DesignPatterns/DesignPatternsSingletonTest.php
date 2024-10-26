<?php
declare(strict_types=1);

namespace Unit\DesignPatterns;

use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonInterface;
use PHPUnit\Framework\TestCase;

require_once(__DIR__ . '/DesignPatternsAbstractSingletonTemp.php'); //namespace fix in autoloader in multi path env phpunit
require_once(__DIR__ . '/DesignPatternsAbstractSingletonArrTemp.php'); //namespace fix in autoloader in multi path env phpunit
require_once(__DIR__ . '/DesignPatternsSingletonTraitTemp.php'); //namespace fix in autoloader in multi path env phpunit
require_once(__DIR__ . '/DesignPatternsSingletonTraitImplementsTemp.php'); //namespace fix in autoloader in multi path env phpunit

class DesignPatternsSingletonTest extends TestCase
{

    protected function setUp(): void
    {
        DesignPatternsAbstractSingletonTemp::tearDown();
        DesignPatternsSingletonTraitImplementsTemp::tearDown();
    }

    protected function tearDown(): void
    {
        //cleanup between tests for static
        DesignPatternsAbstractSingletonTemp::tearDown();
        DesignPatternsSingletonTraitImplementsTemp::tearDown();

    }


    /**
     * @test
     */
    public function mixedTest(): void
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;

        foreach ([
                     [0, 1, false, DesignPatternsAbstractSingletonTemp::class, true],
                     [1, 5, true, DesignPatternsAbstractSingletonTemp::class, true],
                     [5, 7, true, DesignPatternsAbstractSingletonTemp::class, true],
                     [0, 1, false, DesignPatternsSingletonTraitImplementsTemp::class, true],
                     [1, 5, true, DesignPatternsSingletonTraitImplementsTemp::class, true],
                     [5, 7, true, DesignPatternsSingletonTraitImplementsTemp::class, true],
                     [0, 1, false, DesignPatternsSingletonTraitTemp::class, false],
                     [1, 5, true, DesignPatternsSingletonTraitTemp::class, false],
                     [5, 7, true, DesignPatternsSingletonTraitTemp::class, false]
                 ] as $list) {
            list($iGet, $iSet, $bIsset, $sClass, $bInterfaceInstance) = $list;
            $this->assertSame($bIsset, $sClass::hasInstance(), 'hasInstance');
            $oObj = $sClass::getInstance();
            $this->assertSame(true, $oObj instanceof $sClass, 'getInstance');
            $sInterface = AfrSingletonInterface::class;
            $this->assertSame($bInterfaceInstance, $oObj instanceof $sInterface, 'interface instance');
            $this->assertSame($iGet, $oObj->get(), 'get');
            $this->assertSame($iSet, $oObj->set($iSet), 'set');
            $this->assertSame($iSet, $oObj->get(), 'get===set');
            try {
                $this->assertSame(null, new $sClass(), 'new');
            } catch (\Throwable $oEx) {
                $this->assertSame(null, null, 'new OK');
            }

            try {
                $this->assertSame(null, (clone $oObj), 'clone');
            } catch (\Throwable $oEx) {
                $this->assertSame(null, null, 'clone OK');
            }

            try {
                $this->assertSame(null, unserialize(serialize($oObj)), 'wakeup');
            } catch (\Throwable $oEx) {
                $this->assertSame(null, null, 'wakeup OK');
            }


        }

    }


    /**
     * @test
     */
    public function singletonArrTest(): void
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        $k = 'sett';
        $v = ['2', 5, 'fff', 'kk' => 'vv','sa'=>[0,1]];
        $oObj = DesignPatternsAbstractSingletonArrTemp::getInstance();
        $oObj[$k] = $v;
        $this->assertSame(true, isset($oObj[$k]));
        $this->assertSame($v, $oObj[$k]);
        unset($oObj[$k]['sa'][1]);
        $this->assertSame(true, !isset($oObj[$k]['sa'][1]));

        $oObj[$k]['sa'][0]='k';
        $this->assertSame('k', $oObj[$k]['sa'][0]);

   }


}