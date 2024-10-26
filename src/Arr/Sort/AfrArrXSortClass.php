<?php
declare(strict_types=1);

namespace Autoframe\Core\Arr\Sort;

use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonAbstractClass;

class AfrArrXSortClass extends AfrSingletonAbstractClass implements AfrArrXSortInterface
{
    use AfrArrXSortTrait;
}