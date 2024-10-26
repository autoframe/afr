<?php
declare(strict_types=1);

namespace Autoframe\Core\ProcessControl\Worker\Background;

interface AfrBackgroundWorkerInterface
{
    /**
     * Returns /usr/bin/php or C:\xampp\php\php.exe or php
     * @return string
     */
    public static function getPhpBin(): string;

    /**
     * Calls: php $execFileArgs > /dev/null & or widows equivalent
     * @param string $execFileArgs
     * @return void
     */
    public static function execWithArgs(string $execFileArgs): void;
}