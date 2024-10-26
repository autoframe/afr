<?php

namespace Unit;

class AfrClassDependencyExtended extends \Autoframe\Core\ClassDependency\AfrClassDependency
{
    static public function getADependency(): array
    {
        return (array)self::$aDependency;
    }
    static public function getAFatalErr(): array
    {
        return (array)self::$aFatalErr;
    }

    static public function setAFatalErr(string $sFQCN): void
    {
        self::$aFatalErr[$sFQCN] = 'Test Error: ' . __FUNCTION__;
    }

}