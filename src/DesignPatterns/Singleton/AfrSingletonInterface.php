<?php
declare(strict_types=1);

namespace Autoframe\Core\DesignPatterns\Singleton;

use Autoframe\Core\Exception\AfrException;

/**
 * There are 2 methods to use the Singleton pattern:
 * - target class extends AfrSingletonAbstractClass or
 * - use AfrSingletonTrait if the target class is not extendable
 */
interface AfrSingletonInterface
{
    /**
     * Cloning and un-serialization are not permitted for singletons.
     * @throws AfrException
     */
    public function __clone();

    /**
     * @throws AfrException
     */
    public function __wakeup();

    /**
     * The method you use to get the Singleton's instance.
     * @return self
     */
    public static function getInstance(): self;

    /**
     * @return bool
     */
    public static function hasInstance(): bool;
}