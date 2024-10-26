<?php
declare(strict_types=1);

namespace Autoframe\Core\FileSystem\Traversing;

use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonAbstractClass;

class AfrDirTraversingCountChildrenDirsClass extends AfrSingletonAbstractClass implements AfrDirTraversingCountChildrenDirsInterface
{
    use AfrDirTraversingCountChildrenDirsTrait;

}