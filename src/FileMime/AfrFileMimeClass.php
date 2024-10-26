<?php
declare(strict_types=1);

namespace Autoframe\Core\FileMime;

use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonAbstractClass;

class AfrFileMimeClass  extends AfrSingletonAbstractClass implements AfrFileMimeInterface
{
    use AfrFileMimeTrait;
}