<?php
declare(strict_types=1);

namespace Autoframe\Core\FileSystem\DirPath;

use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonAbstractClass;

class AfrDirPathClass extends AfrSingletonAbstractClass implements AfrDirPathInterface
{
    use AfrDirPathTrait;
}