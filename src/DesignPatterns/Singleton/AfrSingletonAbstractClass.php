<?php
declare(strict_types=1);

namespace Autoframe\Core\DesignPatterns\Singleton;

/**
 * There are 2 methods to use the Singleton pattern:
 * - target class extends AfrSingletonAbstractClass or
 * - use AfrSingletonTrait if the target class is not extendable
 */
abstract class AfrSingletonAbstractClass implements AfrSingletonInterface
{
    use AfrSingletonTrait;
}