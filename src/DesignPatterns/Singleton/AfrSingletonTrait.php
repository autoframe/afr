<?php
declare(strict_types=1);

namespace Autoframe\Core\DesignPatterns\Singleton;

use Autoframe\Core\Exception\AfrException;

/**
 * There are 2 methods to use the Singleton pattern:
 * - target class extends AfrSingletonAbstractClass or
 * - use AfrSingletonTrait if the target class is not extendable
 */
trait AfrSingletonTrait
{
    /**
     * The actual singleton's instance almost always resides inside a static
     * field. In this case, the static field is an array, where each subclass of
     * the Singleton stores its own instance.
     */
    protected static array $instances = [];

    /**
     * Singleton's constructor should not be public. However, it can't be
     * private either if we want to allow subclassing.
     */
    final protected function __construct()
    {
    }

    /**
     * Cloning and un-serialization are not permitted for singletons.
     * @throws AfrException
     */
    final public function __clone()
    {
        throw new AfrException('Cannot clone a singleton: ' . static::class);
    }

    /**
     * @throws AfrException
     */
    final public function __wakeup()
    {
        throw new AfrException('Cannot unserialize singleton: ' . static::class);
    }


    /**
     * The method you use to get the Singleton's instance.
     * @return self
     */
    final public static function getInstance(): self
    {
        if (!self::hasInstance()) {
            // Note that here we use the "static" keyword instead of the actual
            // class name. In this context, the "static" keyword means "the name
            // of the current class". That detail is important because when the
            // method is called on the subclass, we want an instance of that
            // subclass to be created here.
            self::$instances[static::class] = new static();
            // TODO add configurable actions with AfrConfig
	        //TODO closure dupa constructor
	        //todo closure la fiecare get instance
            return self::$instances[static::class];
        }
        return self::$instances[static::class];
    }


    /**
     * @return bool
     */
    final public static function hasInstance(): bool
    {
        return !empty(self::$instances[static::class]);
    }
}