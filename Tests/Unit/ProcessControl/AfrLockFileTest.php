<?php
declare(strict_types=1);

namespace Unit\ProcessControl;

use Autoframe\Core\ProcessControl\Lock\AfrLockInterface;
use Autoframe\Core\ProcessControl\Lock\AfrLockFileClass;
use Autoframe\Core\ProcessControl\Worker\Background\AfrBackgroundWorkerClass;
use PHPUnit\Framework\TestCase;

class AfrLockFileTest extends TestCase
{
    protected AfrLockInterface $oLock;
    protected function setUp(): void
    {
        $this->oLock = new AfrLockFileClass(__CLASS__);
    }

    /**
     * @test
     */
    public function AfrLockFileClassSelfTest(): void
    {
        $this->assertSame(false, $this->oLock->isLocked());
        $this->assertSame(true, $this->oLock->releaseLock());
        $this->assertSame(false, $this->oLock->isLocked());
        $this->assertSame(0, $this->oLock->getLockPid());
        $this->assertSame(true, $this->oLock->obtainLock());
        $this->assertSame(true, $this->oLock->isLocked());
        $this->assertSame((int)getmypid(), $this->oLock->getLockPid());
        $this->assertSame(true, $this->oLock->releaseLock());
        $this->assertSame(false, $this->oLock->isLocked());

        unset($this->oLock);
    }

    /**
     * @test
     */
    public function AfrLockFileClassWorkerTest(): void
    {
        $iWorkerLive = 2500;
        $execPhp = __DIR__ . DIRECTORY_SEPARATOR . 'AfrTestWorkerLock.php';
        $sLockName = md5(__FILE__);

        AfrBackgroundWorkerClass::execWithArgs("$execPhp $sLockName $iWorkerLive");
        $oLockWorker = new AfrLockFileClass($sLockName);
        $iWorkerLive += 100; //Wait max 100 ms more for the worker to start
        $iDecreaseMs = 5;
        usleep(15 * 1000);
        while (true) {
            $iWorkerLive -= $iDecreaseMs;
            usleep($iDecreaseMs * 1000);
            if($oLockWorker->isLocked()){
                $this->assertSame(true, $oLockWorker->isLocked());
                $this->assertSame(true, $oLockWorker->getLockPid() > 0);
                $this->assertSame(false, $oLockWorker->obtainLock());
                break;
            }
            if ($iWorkerLive < 0) {
                $this->assertSame(true, 'Failed $oLockWorker->isLocked');
                break;
            }
        }
    }

}