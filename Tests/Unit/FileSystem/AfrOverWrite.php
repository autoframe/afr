<?php
$file = __DIR__ . DIRECTORY_SEPARATOR . 'AfrOverWriteClass.txt';
file_put_contents($file, print_r($_SERVER['argv'], true));
$fp = fopen($file, 'r');
$_SERVER['argv'][1] += 5;
usleep($_SERVER['argv'][1] * 1000);//100ms
//fwrite($fp,'$fp');
fclose($fp);

