<?php
declare(strict_types=1);

namespace Unit\Arr;

use Autoframe\Core\Arr\Sort\AfrArrXSortClass;
use PHPUnit\Framework\TestCase;

class AfrArrXSortTest extends TestCase
{

    static function ArrXSortDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        $aSet = $aTempSort = [
            'aa' => 'Â',
            'a' => 'r\\\'',
            'ca' => 'ă)',
            'A' => 0.,
            'n30' => '0.0',
            'n3' => 'Țg',
            'n31' => '!d',
            'cA' => -2.,
            85 => '&',
            'ș' => 'ț',
            'Ș' => '',
            8 => '\\',
            71 => '%',
            '70.2' => '-2.',
            ';' => -22,
            'â' => 'r',
            '#~' => 'R',
            "\tTAB" => 'a\"a',
            '#  ~' => 'Ă',
            7 => ".0",
            75 => null,
            76 => false,
            77 => true,
        ];


        $out = [];
        foreach ([SORT_NATURAL, SORT_REGULAR] as $flags) {
            $aTempSort = $aSet;
            asort($aTempSort, $flags);
            $out['asort' . count($out)] = [$aSet, SORT_ASC, false, $flags, $aTempSort];

            $aTempSort = $aSet;
            arsort($aTempSort, $flags);
            $out['arsort' . count($out)] = [$aSet, SORT_DESC, false, $flags, $aTempSort];

            $aTempSort = $aSet;
            ksort($aTempSort, $flags);
            $out['ksort' . count($out)] = [$aSet, SORT_ASC, true, $flags, $aTempSort];

            $aTempSort = $aSet;
            krsort($aTempSort, $flags);
            $out['krsort' . count($out)] = [$aSet, SORT_DESC, true, $flags, $aTempSort];
        }

        $fn = function ($a, $b) {
            return strnatcmp((string)$a, (string)$b);
        };

        $aTempSort = $aSet;
        uasort($aTempSort, $fn);
        $out['uasort' . count($out)] = [$aSet, $fn, false, 0, $aTempSort];

        $aTempSort = $aSet;
        uksort($aTempSort, $fn);
        $out['uksort' . count($out)] = [$aSet, $fn, true, 0, $aTempSort];

        $aTempSort = $aSet;
        natsort($aTempSort);
        $out['natsort' . count($out)] = [$aSet, 'natsort', false, 0, $aTempSort];

        $aTempSort = $aSet;
        natcasesort($aTempSort);
        $out['natcasesort' . count($out)] = [$aSet, 'natcasesort', false, 0, $aTempSort];

        /**
         * Sort by value if class is found normally fails, but we can switch to sort by key:
         */
        $oClass = new \stdClass();
        $oClass->data = 1;
        $aSet[71] = $oClass;

        foreach ([SORT_NATURAL, SORT_REGULAR] as $flags) {
            $aTempSort = $aSet;
            ksort($aTempSort, $flags);
            $out['detectSortByKey-ksort' . count($out)] = [$aSet, SORT_ASC, null, $flags, $aTempSort];

            $aTempSort = $aSet;
            krsort($aTempSort, $flags);
            $out['detectSortByKey-krsort' . count($out)] = [$aSet, SORT_DESC, null, $flags, $aTempSort];
        }

        return $out;
    }

    /**
     * @test
     * @dataProvider ArrXSortDataProvider
     */
    public function ArrXSortTest($aArray, $mDirectionOrCallableFn, $mSortByKey, $flags, $aExpectedSort): void
    {
        AfrArrXSortClass::getInstance()->arrayXSort($aArray, $mDirectionOrCallableFn, $mSortByKey, $flags);
        $this->assertSame(serialize($aArray), serialize($aExpectedSort), print_r(func_get_args(), true));
    }


}