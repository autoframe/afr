<?php

use Autoframe\Core\Exception\AfrException;

class GlobalMockSingleton extends GlobalMockClass2
{
    private static array $instances = [];

    /**
     * Singleton's constructor should not be public. However, it can't be
     * private either if we want to allow subclassing.
     */
    protected function __construct()
    {
    }

    /**
     * Cloning and unserialization are not permitted for singletons.
     * @throws AfrException
     */
    protected function __clone()
    {
        throw new AfrException('Cannot clone a singleton');
    }

    /**
     * @throws AfrException
     */
    public function __wakeup()
    {
        throw new AfrException("Cannot unserialize singleton");
    }


    /**
     * The method you use to get the Singleton's instance.
     * @return self
     */
    public static function getInstance(): object
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            return self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }

}