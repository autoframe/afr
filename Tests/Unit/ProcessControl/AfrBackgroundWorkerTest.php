<?php
declare(strict_types=1);

namespace Unit\ProcessControl;

use Autoframe\Core\ProcessControl\Worker\Background\AfrBackgroundWorkerClass;
use PHPUnit\Framework\TestCase;

class AfrBackgroundWorkerTest extends TestCase
{

    /**
     * @test
     */
    public function overWriteFileTest(): void
    {
        $iWorkers = 2;
        $iWorkerLive = 6500;
        $execPhp = __DIR__ . DIRECTORY_SEPARATOR . 'AfrTestWorkerPc.php';
        $aWorkerFiles = [];
        for ($i = 1; $i <= $iWorkers; $i++) {
            $aWorkerFiles[$i] = $sFile = ('AfrTestWorkerPc.' . $iWorkers . '_' . $i . '.test');
            if (is_file($aWorkerFiles[$i])) {
                unlink($aWorkerFiles[$i]);//prevent left overs...
            }
            AfrBackgroundWorkerClass::execWithArgs("$execPhp $sFile $iWorkerLive");
        }
        $iDecreaseMs = 10;
        while (true) {
            $iWorkerLive -= $iDecreaseMs;
            usleep($iDecreaseMs * 1000);
            foreach ($aWorkerFiles as $i => $sFile) {
                $sFile = __DIR__ . DIRECTORY_SEPARATOR . $sFile;
                if (is_file($sFile)) {
                    unset($aWorkerFiles[$i]);
                    @unlink($sFile);
                }
            }
            if ($iWorkerLive < 0 || count($aWorkerFiles) < 1) {
                break;
            }
        }
        $this->assertSame(0, count($aWorkerFiles));
    }

}