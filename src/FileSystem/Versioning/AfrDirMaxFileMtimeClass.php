<?php
declare(strict_types=1);

namespace Autoframe\Core\FileSystem\Versioning;

use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonAbstractClass;

class AfrDirMaxFileMtimeClass extends AfrSingletonAbstractClass implements AfrDirMaxFileMtimeInterface
{
    use AfrDirMaxFileMtimeTrait;
}