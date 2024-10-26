<?php
declare(strict_types=1);

namespace Unit;

use Autoframe\Core\FileSystem\Exception\AfrFileSystemException;
use Autoframe\Core\FileSystem\SplitMerge\AfrSplitMergeClass;
use Autoframe\Core\FileSystem\SplitMerge\AfrSplitMergeInterface;
use Autoframe\Core\FileSystem\SplitMerge\Exception\AfrFileSystemSplitMergeException;
use PHPUnit\Framework\TestCase;

class AfrSplitMergeClassTest extends TestCase
{

    public static function splitDataProvider(): array
    {
        echo __CLASS__ . '->' . __FUNCTION__ . PHP_EOL;

        $sLocalPath = __DIR__ . DIRECTORY_SEPARATOR . 'splitTest' . DIRECTORY_SEPARATOR . 'SplitMerge' . DIRECTORY_SEPARATOR;
        $sLocalPathOther = $sLocalPath . 'Split-In-Other-Path';
        if (!is_dir($sLocalPathOther)) {
            mkdir($sLocalPathOther, 0777, true);
        }
        if (!file_exists($sLocalPath . '../.gitignore')) {
            file_put_contents($sLocalPath . '../.gitignore', "SplitMerge\nCopyMerge\n");
        }

        $sContents1 = '';
        $iParts = 15;
        for ($i = 0; $i < $iParts; $i++) {
            $sContents1 .= (string)$i;
        }
        $iBytes = 2;
        $aTests = [];

        $aTests [] = [
            $sLocalPath . 'sContents1.txt',
            '',
            $sContents1,
            $iBytes,
            [
                'return' => (int)ceil(strlen($sContents1) / $iBytes),
                'filesize' => ['.10|' . $iBytes],
                'contents' => ['.10|14'],
                'unlinkSplitSource' => true,
                'unlinkMergeShards' => true,
            ],

        ];
        $sContents2 = substr($sContents1, 0, 3);
        $aTests [] = [

            $sLocalPath . 'sContents2.txt',
            '',
            $sContents2,
            $iBytes,
            [
                'return' => (int)ceil(strlen($sContents2) / $iBytes),
                'filesize' => ['.1|' . $iBytes, '.2|1'],
                'contents' => ['.2|2', '.1|01'],
                'unlinkMergeShards' => false,
            ],

        ];

        $iBytes = 55555;
        $aTests [] = [
            $sLocalPath . 'sContentsNotToBeSplit.txt',
            $sLocalPathOther,
            $sContents1,
            $iBytes,
            [
                'return' => (int)ceil(strlen($sContents1) / $iBytes),
                'filesize' => ['|' . strlen($sContents1),],
                'contents' => ['|' . $sContents1,],
                'unlinkSplitSource' => true,
                'unlinkMergeShards' => true,

            ],

        ];
        return $aTests;
    }

    /**
     * @test
     * @dataProvider splitDataProvider
     */
    public function splitTest(
        string $sourcePath,
        string $destinationDir,
        string $sTestContents,
        int    $sizeBytes,
        array  $aCheck,
        bool   $bOverwrite = true
    ): void
    {
        file_put_contents($sourcePath, $sTestContents);
        $sBasename = basename($sourcePath);
        $oSplit = AfrSplitMergeClass::getInstance();
        $this->assertSame(true, $oSplit instanceof AfrSplitMergeInterface);
        try {
            $iNumberOfShards = $oSplit->split(
                $sourcePath,
                $sizeBytes,
                $bOverwrite,
                $destinationDir,
                !empty($aCheck['unlinkSplitSource'])
            );
        } catch (AfrFileSystemSplitMergeException $e) {
            $this->assertSame('Not exception', get_class($e), $e->getMessage());
            return;
        }

        if (isset($aCheck['return'])) {
            $this->assertSame(
                $aCheck['return'],
                $iNumberOfShards,
                'number of split parts is wrong: expected(' . $aCheck['return'] .
                ') but received(' . $iNumberOfShards . ') ' .
                $sourcePath
            );
        }
        if (!empty($aCheck['filesize'])) {
            foreach ($aCheck['filesize'] as $sFilesize) {
                $aFilesize = explode('|', $sFilesize);
                $path = $destinationDir ? ($destinationDir . '/' . $sBasename . $aFilesize[0]) : ($sourcePath . $aFilesize[0]);
                $this->assertSame((int)$aFilesize[1], filesize($path), 'split size mismatch: ' . $path);
            }
        }
        if (!empty($aCheck['contents'])) {
            foreach ($aCheck['contents'] as $sContents) {
                $aContents = explode('|', $sContents);
                $path = $destinationDir ? ($destinationDir . '/' . $sBasename . $aContents[0]) : ($sourcePath . $aContents[0]);

                $this->assertSame((string)$aContents[1], file_get_contents($path), 'split contents mismatch: ' . $path);
            }
        }
        $this->assertSame(empty($aCheck['unlinkSplitSource']), file_exists($sourcePath),
            'The split source should be ' . (empty($aCheck['unlinkSplitSource']) ? 'present' : 'deleted') . ' @ ' . $sourcePath);

        ////////// MERGE:

        $iMerged = 0;
        $path = ($destinationDir ? ($destinationDir . '/' . $sBasename) : $sourcePath) .
            ($iNumberOfShards > 1 ? '.' . str_pad(
                    '1',
                    strlen($aFilesize[0]) - 1,
                    '0',
                    STR_PAD_LEFT
                ) : '');
        try {
            $eInfo = "oSplit->merge($path, $destinationDir, $bOverwrite)";
            $iMerged = $oSplit->merge($path, $destinationDir, $bOverwrite, true, !empty($aCheck['unlinkMergeShards']));
        } catch (AfrFileSystemSplitMergeException $e) {
            $this->assertSame('Not exception', get_class($e), $e->getMessage() . "\n" . $eInfo);
        }
        if ($iMerged > 0) {
            $sMergedContents = file_get_contents(($destinationDir ? ($destinationDir . '/' . $sBasename) : $sourcePath));
            $this->assertSame($sTestContents, $sMergedContents, 'merged contents differ from original contents! ' . "\n$sTestContents\n$sMergedContents");

            $this->assertSame(
                empty($aCheck['unlinkMergeShards']),
                file_exists($path) && !(!empty($aCheck['unlinkMergeShards']) && $iNumberOfShards < 2),
                'The split shards should be ' . (empty($aCheck['unlinkMergeShards']) ? 'present' : 'deleted') . ' @ ' . $path);
        } else {
            $this->assertSame($iMerged > 0, $iMerged === 0,'expresion returned "'.$iMerged.'" when running '.$eInfo);
        }

    }


}