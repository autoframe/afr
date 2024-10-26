<?php

namespace Unit\DesignPatterns;

use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonAbstractClass;

class DesignPatternsAbstractSingletonTemp extends AfrSingletonAbstractClass
{

    public int $iData = 0;

    public function get(): int
    {
        return $this->iData;
    }

    public function set(int $iData): int
    {
        return $this->iData = $iData;
    }

    public static function tearDown(): void
    {
        foreach (self::$instances as $key=>$oInstance){
            unset($oInstance);
            unset(self::$instances[$key]);
        }
    }
}