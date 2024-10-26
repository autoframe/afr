<?php
declare(strict_types=1);

namespace Unit\Arr;

use Autoframe\Core\Arr\Export\AfrArrExportArrayAsStringClass;
use PHPUnit\Framework\TestCase;

class AfrArrExportArrayAsStringTest extends TestCase
{

    static function exportPhpArrayAsStringProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        $oClass = serialize(new \stdClass());
        $aSet = [
            'aa' => 'Â',
            'a' => 'r\\\'',
            '\ca' => 'ă)',
            'A' => 0.,
            'n30' => '0.0',
            null => 'Țg',
            'class' => $oClass,
            'subA' =>[
                'cA' => -2.,
                85 => null,
                'ș' => 'ț',
            ],
            'Ș' => '&',
            8 => '\\',
            71 => '%',
            ';' => -22.3,
            '#~' => 'R',
            "\tTAB" => 'a\\"a',
            '#  ~' => 'Ă',
            7 => ".0",
            '71.2'."\n" => '-2',
            'â' => "\n".'r',

        ];
        return [[
            $aSet,
        ]];


    }

    /**
     * @test
     * @dataProvider exportPhpArrayAsStringProvider
     */
    public function exportPhpArrayAsStringTest(array $aOriginal): void
    {
        $sArray = AfrArrExportArrayAsStringClass::getInstance()->exportPhpArrayAsString($aOriginal);
        $aData = [];
        eval($sArray);
        $s1 = serialize($aOriginal);
        $s2 = serialize($aData);
        $this->assertSame($s1,$s2);
    }

    /**
     * @test
     * @dataProvider exportPhpArrayAsStringProvider
     */
    public function exportPhpArrayAsStringLimitationTest(array $aOriginal): void
    {
        $aOriginal['class'] = unserialize($aOriginal['class']);
        $sArray = AfrArrExportArrayAsStringClass::getInstance()->exportPhpArrayAsString($aOriginal);
        $aData = [];
        eval($sArray);
        $s1 = serialize($aOriginal);
        $s2 = serialize($aData);
        $this->assertNotEquals($s1,$s2);
    }

}