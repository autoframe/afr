<?php
declare(strict_types=1);

namespace Autoframe\Core\FileSystem\SplitMerge;

use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonAbstractClass;

class AfrSplitMergeClass extends AfrSingletonAbstractClass implements AfrSplitMergeInterface
{
    use AfrSplitMergeTrait;
}