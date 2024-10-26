<?php
declare(strict_types=1);

namespace Autoframe\Core\FileSystem\Traversing;

use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonAbstractClass;

class AfrDirTraversingFileListClass extends AfrSingletonAbstractClass implements AfrDirTraversingFileListInterface
{
    use AfrDirTraversingFileListTrait;
}