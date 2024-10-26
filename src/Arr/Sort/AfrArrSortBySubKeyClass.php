<?php
declare(strict_types=1);

namespace Autoframe\Core\Arr\Sort;

use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonAbstractClass;

/**
 * Sort by a key or property owned by a first level array element
 * AfrArrSortBySubKeyClass->arraySortBySubKey(array $aMultiLevelToSort, string $sSubArrayKey, int $iOrder = SORT_ASC, int $iFlag = SORT_REGULAR): array;
 */
class AfrArrSortBySubKeyClass extends AfrSingletonAbstractClass implements AfrArrSortBySubKeyInterface
{
    use AfrArrSortBySubKeyTrait;
}