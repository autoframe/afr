<?php
declare(strict_types=1);

namespace Unit\Arr;

use Autoframe\Core\Arr\Merge\AfrArrMergeProfileClass;
use PHPUnit\Framework\TestCase;

class AfrArrMergeProfileTest extends TestCase
{

    static function arrayMergeProfileProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        $aReturn = [];
        $aReturn[] = [
            //ORIGINAL
            [
                'original_k' => 'original value',
                'original_kArrUnkeyed' => ['vOrig1', 'VOrig2'],
                'original_kArrKeyed' => ['ok1' => 'vkOrig1', 'ok2' => ['VOrig2','x'=>'Vorig3'], 'VOrig3'],
                'origUnkeyd1',
                'origUnkeyd2',
                1
            ],
            //NEW
            [
                'new_k' => 'new value',
                'original_k' => 'overwrittenNew',
                'new_kArrUnkeyed' => ['vNew1', 'New2'],
                'original_kArrKeyed' => ['nk1' => 'vkNew1', 'nk2' => 'VNew2', 'VNew3','ok1'=>'overwritten','ok2' => ['x'=>'VNew3']],
                'newUnkeyd1',
                'newUnkeyd2',
                2
            ],
            //EXPECTED
            [
                'original_k' => 'overwrittenNew',
                'original_kArrUnkeyed' => ['vOrig1', 'VOrig2'],
                'original_kArrKeyed' => [
                    'ok1' => 'overwritten',
                    'ok2' => [0 => 'VOrig2', 'x' => 'VNew3'],
                    0 => 'VOrig3',
                    'nk1' => 'vkNew1',
                    'nk2' => 'VNew2',
                    1 => 'VNew3'
                ],
                0 => 'origUnkeyd1',
                1 => 'origUnkeyd2',
                2 => 1,
                'new_k' => 'new value',
                'new_kArrUnkeyed' => ['vNew1', 'New2'],
                3 => 'newUnkeyd1',
                4 => 'newUnkeyd2',
                5 => 2,
            ],
        ];
        return $aReturn;


    }

    /**
     * @test
     * @dataProvider arrayMergeProfileProvider
     */
    public function arrayMergeProfileTest(array $aOriginal, array $aNew, array $aExpected): void
    {
        $aMerged = AfrArrMergeProfileClass::getInstance()->arrayMergeProfile($aOriginal, $aNew);
        $this->assertSame(serialize($aMerged), serialize($aExpected));
    }


}