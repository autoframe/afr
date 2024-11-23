<?php

require_once __DIR__ . '/vendor/autoload.php';


if (strpos($_SERVER['REQUEST_URI'] ?? '', 'opcache')) {
	echo '<pre>';
	print_r(opcache_get_status());
	echo '</pre>';
} elseif (strpos($_SERVER['REQUEST_URI'] ?? '', 'pi~')) {
	phpinfo();
}




use Autoframe\Core\Afr\Afr;
use Autoframe\Core\Tenant\AfrTenant;
use Autoframe\Core\FsCache\AfrIncPhpCache;

$oc = AfrIncPhpCache::getInstance();
$k = '\Composer\Autoload\ClassLoader';
var_dump($oc->get($k));
//$oc->put($k, get_declared_classes(),15); die;

//$_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);
define('AFR_BASE_DIR',__DIR__.DIRECTORY_SEPARATOR.'base1');

new Afr();

$aReport = Afr::app()->run(); //print_r($aReport);


echo PHP_EOL.
	'E.time: '.
	number_format(
		(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'])*1000  ,
		3
	).' MS @END'.PHP_EOL;
echo '<br><pre>';
print_r(AfrTenant::getProtocolDomain());
//$l = print_r(array_merge(get_declared_classes(),get_declared_interfaces(),get_declared_traits()),true);
//echo str_repeat($l,20);
//var_dump($_ENV);
//AfrTenant::initFileSystem();
//print_r($_SERVER);

echo '</pre>';


