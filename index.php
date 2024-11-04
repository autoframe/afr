<?php

require_once __DIR__ . '/vendor/autoload.php';
use Autoframe\Core\App\AfrBaseIndex;
use Autoframe\Core\Tenant\AfrTenant;

//$_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);

$aReport = (new AfrBaseIndex(__DIR__.DIRECTORY_SEPARATOR.'base'))->run(); print_r($aReport);

$t = (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'])*1000;
echo 'Execution time: '.number_format($t,3).' MS<br><pre>';

//print_r(array_merge(get_declared_classes(),get_declared_interfaces(),get_declared_traits()));
//var_dump($_ENV);
AfrTenant::initFileSystem();
print_r($_SERVER);
print_r(AfrTenant::getProtocolDomain());

echo '</pre>';