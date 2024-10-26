<?php
declare(strict_types=1);

namespace Autoframe\Core\ProcessControl\Worker\Background;

trait AfrBackgroundWorkerTrait
{
    /**
     * Returns /usr/bin/php or C:\xampp\php\php.exe or php
     * @return string
     */
    public static function getPhpBin(): string
    {
        $php = 'php';
        if (DIRECTORY_SEPARATOR === '\\') { //Windows
            $ini = php_ini_loaded_file();
            $exe = $ini ? (substr($ini, 0, -3) . 'exe') : '';
            if ($exe && is_file($exe)) {
                $php = $exe;
            }
            return 'start /B ' . $php;
        } else { //Unix
            if (is_file($phpBin = '/usr/bin/php')) {
                return $phpBin;
            }
        }
        return $php;
    }

    /**
     * Calls: php $execFileArgs > /dev/null & or widows equivalent
     * @param string $execFileArgs
     * @return void
     */
    public static function execWithArgs(string $execFileArgs): void
    {
        $call = self::getPhpBin() . ' ' . trim($execFileArgs);
        if (DIRECTORY_SEPARATOR === '\\') { //windows
            pclose(popen($call, 'r'));
        } else { //unix
            exec($call . ' > /dev/null &');
        }
    }

}