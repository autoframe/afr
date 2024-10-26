<?php
declare(strict_types=1);

namespace Autoframe\Core\Arr\Sort;

interface AfrArrSortBySubKeyInterface
{
    /**
     * Sort by a key or property owned by a first level array element
     * @param array $aMultiLevelToSort
     * @param string $sSubArrayKey
     * @param int $iOrder
     * @param int $iFlag
     * @return array
     */
    public function arraySortBySubKey(
        array $aMultiLevelToSort,
        string $sSubArrayKey,
        int $iOrder = SORT_ASC,
        int $iFlag = SORT_REGULAR
    ): array;

}