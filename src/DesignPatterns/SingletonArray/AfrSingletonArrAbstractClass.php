<?php
declare(strict_types=1);

namespace Autoframe\Core\DesignPatterns\SingletonArray;

use Autoframe\Core\DesignPatterns\ArrayAccess\AfrObjectArrayAccessTrait;
use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonAbstractClass;
use Countable;
use Iterator;
use ArrayAccess;

abstract class AfrSingletonArrAbstractClass extends AfrSingletonAbstractClass implements ArrayAccess, Iterator, Countable
{
    use AfrObjectArrayAccessTrait;
}