<?php
declare(strict_types=1);

namespace Autoframe\Core\FileSystem\Recursive;

trait AfrRecursiveRmDir
{


    protected function recursiveRmDir(string $sDirPath): bool
    {

        if (
            substr($sDirPath, 0, 2) === '\\\\' || # Detect Windows network path \\192.168.0.1\share
            substr($sDirPath, 1, 2) == ':\\'  #Detect Windows drive path C:\Dir
        ) {
            $sDs = '\\';
        } else {
            $iWinDs = substr_count($sDirPath, '\\');
            $iUnixDs = substr_count($sDirPath, '/');
            if ($iWinDs < 1 && $iUnixDs < 1) {
                $sDs = DIRECTORY_SEPARATOR;
            } else {
                $sDs = $iWinDs > $iUnixDs ? '\\' : '/';
            }
        }

        $sDirPath = rtrim(strtr($sDirPath, ($sDs === '/' ? '\\' : '/'), $sDs), '\/');

        if (
            strlen($sDirPath) < 1 ||  //or current dir or unknown
            in_array($sDirPath, ['.', '..', '/', '\\']) || //root or current dir
            strlen($sDirPath) < 4 && substr($sDirPath, 1, 2) == ':\\' // windows drive
        ) {
            return false;
        }


        if (@filetype($sDirPath) !== 'dir') {
            return false;
        }

        $aDirHasChildren = [];
        $handle = opendir($sDirPath);
        while (false !== ($item = readdir($handle))) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $sCurrentItem = $sDirPath . $sDs . $item;
            $sItemType = @filetype($sCurrentItem); //Possible values are fifo, char, dir, block, link, file, socket and unknown.
            if ($sItemType === 'dir') {
                if (!$this->recursiveRmDir($sCurrentItem)) {
                    $aDirHasChildren[] = $sCurrentItem;
                }
            } else {
                if (!unlink($sCurrentItem)) {
                    $aDirHasChildren[] = $sCurrentItem;
                }
            }
        }
        closedir($handle);
        if (count($aDirHasChildren)) {
            return false;
        }
        return rmdir($sDirPath);
    }


}