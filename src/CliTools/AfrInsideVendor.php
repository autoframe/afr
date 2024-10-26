<?php

namespace Autoframe\Core\CliTools;

/**
 * Class AfrInsideVendor
 *
 * This class provides a static method to check if a given file path is inside a 'vendor' directory.
 *
 * @package YourPackage
 */
class AfrInsideVendor
{
    /**
     * Check if the given path is located inside the vendor directory.
     *
     * @param string $sPath The path to check.
     * @return bool True if the path is inside the vendor directory, false otherwise.
     */
    public static function pathIsInsideVendorDir(string $sPath): bool
    {
        return strpos(
                str_replace(
                    ['\\vendor', 'vendor\\',],
                    ['/vendor', 'vendor/',],
                    $sPath.'/'
                ),
                '/vendor/'
            ) !== false;
   }
}