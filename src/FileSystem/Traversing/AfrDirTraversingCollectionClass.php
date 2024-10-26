<?php
declare(strict_types=1);

namespace Autoframe\Core\FileSystem\Traversing;

use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonAbstractClass;

class AfrDirTraversingCollectionClass extends AfrSingletonAbstractClass implements AfrDirTraversingCollectionInterface
{
    use AfrDirTraversingCollectionTrait;
}