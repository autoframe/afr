<?php
declare(strict_types=1);

namespace Autoframe\Core\Arr\Merge;

/**
 * Recursive array merging for config profiles
 * $aMergedProfile = $this->arrayMergeProfile(array $aOriginal, array $aNew);
 */
interface AfrArrMergeProfileInterface
{
    /**
     * Recursive array merging for config profiles
     * @param array $aOriginal
     * @param array $aNew
     * @return array
     */
    public function arrayMergeProfile(array $aOriginal, array $aNew): array;
}