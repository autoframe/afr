<?php
declare(strict_types=1);

namespace Autoframe\Core\FileSystem\Versioning;

use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonAbstractClass;

class AfrFileVersioningMtimeHashClass extends AfrSingletonAbstractClass implements AfrFileVersioningMtimeHashInterface
{
    use AfrFileVersioningMtimeHashTrait;
}