<?php
declare(strict_types=1);

namespace Autoframe\Core\Arr\Export;

use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonAbstractClass;

class AfrArrExportArrayAsStringClass extends AfrSingletonAbstractClass implements AfrArrExportArrayAsStringInterface
{
    use AfrArrExportArrayAsStringTrait;
}