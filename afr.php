<?php

require_once __DIR__ . '/vendor/autoload.php';

use Autoframe\Core\Afr\Afr;

new Afr(__DIR__);
$aReport = Afr::app()->run(); //print_r($aReport);
