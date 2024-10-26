<?php

namespace Autoframe\Core\MockClasses;

class MockSubclass33 implements MockSubclass3Interface
{
    public int $iVal;
    public function __construct()
    {
        echo __CLASS__.'->'.__FUNCTION__.PHP_EOL;
        $this->iVal = time();
    }

    /**
     * @return int
     */
    public function getIVal(): int
    {
        return $this->iVal;
    }

}