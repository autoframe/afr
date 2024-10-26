<?php
declare(strict_types=1);

namespace Unit\Arr;

use Autoframe\Core\Arr\Sort\AfrArrSortBySubKeyClass;
use PHPUnit\Framework\TestCase;

class AfrArrSortBySubKeyTest extends TestCase
{

    static function arraySortBySubKeyProvider(): array
    {
        $people = array(
            12345 => array(
                'id' => 12345,
                'first_name' => 'Joe',
                'surname' => 'Xloggs',
                'age' => 23,
                'sex' => 'm'
            ),
            12346 => array(
                'id' => 12346,
                'first_name' => 'Adam',
                'surname' => 'Smith',
                'age' => 18,
                'sex' => 'm'
            ),
            12347 => array(
                'id' => 12347,
                'first_name' => 'Amy',
                'surname' => 'Jones',
                'age' => 21,
                'sex' => 'f'
            )
        );
        $i = new \stdClass();
        $i->name = 'Ion';
        $b = new \stdClass();
        $b->name = 'Bella';
        $classes = ['i' => $i, 'b' => $b];

        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;
        return [
            [$people, 'age', SORT_DESC, [12345, 12347, 12346]],
            [$people, 'surname', SORT_ASC, [12347, 12346, 12345]],
            [$classes, 'name', SORT_ASC, ['b','i']],
        ];
    }

    /**
     * @test
     * @dataProvider arraySortBySubKeyProvider
     */
    public function arraySortBySubKeyTest(array $aToSort, string $sKey, int $iDirection, array $aExpectedIndexOrder): void
    {
        $aExpected = [];
        foreach($aExpectedIndexOrder as $mKey){
            $aExpected[$mKey] =  $aToSort[$mKey];
        }
        $aSorted = AfrArrSortBySubKeyClass::getInstance()->arraySortBySubKey($aToSort,$sKey,$iDirection);
        $this->assertSame($aSorted, $aExpected, print_r(func_get_args(), true));
    }



}