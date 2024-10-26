<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\FileSystem\Exception\AfrFileSystemException;
use Autoframe\Core\FileSystem\Recursive\AfrRecursiveRmDir;
use Autoframe\Core\FileSystem\SplitMergeCopyDir\AfrSplitMergeCopyDirClass;
use Autoframe\Core\FileSystem\SplitMergeCopyDir\AfrSplitMergeCopyDirInterface;
use Autoframe\Core\FileSystem\SplitMergeCopyDir\Exception\AfrFileSystemSplitMergeCopyDirException;
use PHPUnit\Framework\TestCase;

class AfrSplitMergeClassCopyDirTest extends TestCase
{
    use AfrRecursiveRmDir;

    public static function splitDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;

        $sDirBase = __DIR__ . DIRECTORY_SEPARATOR . 'splitTest' . DIRECTORY_SEPARATOR . 'CopyMerge' . DIRECTORY_SEPARATOR;
        $dirFromPath = $sDirBase . 'SplitCopyFrom';
        $dirFromPathSd = $dirFromPath . DIRECTORY_SEPARATOR . 'SubDirLvL2';
        $dirToPath = $sDirBase . 'SplitCopyTo';
        $dirInplacePath = $sDirBase . 'SplitMergeInplace';
        $dirToMergePath = $sDirBase . 'SplitMergeTo';

        foreach ([$dirFromPathSd, $dirToPath, $dirToPath . '2', $dirToMergePath, $dirToMergePath . '2', $dirInplacePath] as $dp) {
            if (!is_dir($dp)) {
                mkdir($dp, 0777, true);
            }
        }

        $sContents = '';
        $iParts = 15;
        for ($i = 0; $i < $iParts; $i++) {
            $sContents .= (string)$i;
        }

        file_put_contents($dirFromPathSd . '/sCopyDirL2.txt', $sContents);
        file_put_contents($dirFromPath . '/sCopyDirL1.txt', substr($sContents, 0, 3));


        $aTests = [];

        $aTests [] = [
            $dirFromPath,
            $dirToPath,
            $dirToMergePath,
            $dirInplacePath,
            8
        ];

        return $aTests;
    }

    /**
     * @test
     * @dataProvider splitDataProvider
     */
    public function splitTest(
        string $sourceDirPath,
        string $sSplitDestinationPath,
        string $sMergeDestinationPath,
        string $dirInplacePath,
        int    $sizeBytes
    ): void
    {

        $bOverwrite = true;
        $oSplit = AfrSplitMergeCopyDirClass::getInstance();
        $this->assertSame(true, $oSplit instanceof AfrSplitMergeCopyDirInterface);
        //$return = $oSplit->split($sourcePath, $sizeBytes, $bOverwrite);
        $iFiles = $oSplit->splitCopyDir($sourceDirPath, $sSplitDestinationPath, $sizeBytes, $bOverwrite);
        $this->assertSame(2, $iFiles);
        $this->dirBulkCheck($sSplitDestinationPath,true);

        $oSplit->splitCopyDir($sourceDirPath, $sSplitDestinationPath . '2', $sizeBytes, $bOverwrite);
        $this->dirBulkCheck($sSplitDestinationPath,true);

        $oSplit->splitCopyDir($sourceDirPath, $dirInplacePath, $sizeBytes, $bOverwrite);
        $this->dirBulkCheck($sSplitDestinationPath,true);


        $oSplit->mergeCopyDir($sSplitDestinationPath, $sMergeDestinationPath, true, false);
        $this->dirBulkCheck($sMergeDestinationPath,false);


        $oSplit->mergeCopyDir($sSplitDestinationPath . '2', $sMergeDestinationPath . '2', true, true);
        $this->dirBulkCheck($sMergeDestinationPath . '2',false);

        $oSplit->mergeCopyDir($dirInplacePath, '', $bOverwrite, false);
        $this->dirBulkCheck($dirInplacePath,false);

        $this->recursiveRmDir(__DIR__ . DIRECTORY_SEPARATOR . 'splitTest' . DIRECTORY_SEPARATOR . 'CopyMerge' );


    }

    protected function dirBulkCheck(string $sMergeDestinationPath, bool $bShards): void
    {
        $this->assertSame(true, file_exists($sMergeDestinationPath . '/sCopyDirL1.txt'));
        $this->assertSame(!$bShards, file_exists($sMergeDestinationPath . '/SubDirLvL2/sCopyDirL2.txt'));
        $this->assertSame($bShards, file_exists($sMergeDestinationPath . '/SubDirLvL2/sCopyDirL2.txt.1'));
        $this->assertSame($bShards, file_exists($sMergeDestinationPath . '/SubDirLvL2/sCopyDirL2.txt.2'));
        $this->assertSame($bShards, file_exists($sMergeDestinationPath . '/SubDirLvL2/sCopyDirL2.txt.3'));
    }

}