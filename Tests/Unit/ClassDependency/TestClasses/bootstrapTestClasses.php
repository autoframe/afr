<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'AfrClassDependencyExtended.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GlobalMockInterfaceExa.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GlobalMockInterfaceExb.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GlobalMockInterface.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GlobalMockTraitSub.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GlobalMockTrait.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GlobalMockAbstract.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GlobalMockClass.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GlobalMockClass2.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'GlobalMockSingleton.php';



if (PHP_VERSION_ID >= 81000) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'GlobalMockEnum.php';
}