<?php
declare(strict_types=1);

namespace Autoframe\Core\ProcessControl\Lock;


class AfrLockFileClass implements AfrLockInterface
{
    protected string $sLockPath;
    protected string $sPidPath;
    protected $writeLockedFilePointer = null;


    public function __construct(string $sLockName, array $aContextData = [])
    {
        $sTempDir = (string)ini_get('sys_temp_dir');
        if (!$sTempDir) {
            $sTempDir = sys_get_temp_dir();
        }
        if (!$sTempDir) {
            $sTempDir = __DIR__;
        }

        $sPath = rtrim($sTempDir, '\\/') .
            DIRECTORY_SEPARATOR .
            md5(
                $sLockName . serialize($aContextData)
            );
        $this->sLockPath = $sPath . '.lock';
        $this->sPidPath = $sPath . '.pid';

    }

    public function __destruct()
    {
        if ($this->writeLockedFilePointer) {
            //  $this->releaseLock(); //GC can run before script end, so isLocked will fail
            //  DEAD MAN SWITCH OFF
            register_shutdown_function(function () {
                if (empty($this->writeLockedFilePointer)) {
                    return;
                }
                fclose($this->writeLockedFilePointer);
                if (is_file($this->sLockPath)) {
                    unlink($this->sLockPath);
                }
                if (is_file($this->sPidPath)) {
                    unlink($this->sPidPath);
                }
            });
        }
    }

    /**
     * Check if the lock is in place
     * @return bool
     */
    public function isLocked(): bool
    {
        if (!is_file($this->sLockPath)) {
            return false;
        }
        if ($this->writeLockedFilePointer) {
            return true;
        }
        $fp = fopen($this->sLockPath, 'w+');
        if (!$fp) {
            return true;
        }
        if (flock($fp, LOCK_EX | LOCK_NB)) {
            fclose($fp);
            return false;
        }
        return true;
    }

    /**
     * Creates a new lock or fails
     * @return bool
     */
    public function obtainLock(): bool
    {
        if ($this->writeLockedFilePointer) {
            return true;
        }

        $fp = fopen($this->sLockPath, 'w+');
        if (!$fp) {
            return false;
        }
        // Activate the LOCK_NB option on an LOCK_EX operation
        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            return false;
        }
        $sPid = (string)(int)getmypid();
        fwrite($fp, $sPid);
        file_put_contents($this->sPidPath, $sPid);
        $this->writeLockedFilePointer = $fp;
        return true;

    }

    /**
     * Returns false if lock is in place and the lock file can't be closed
     * Returns true if there is no lock in place or operation was successfully made.
     * @return bool
     */
    public function releaseLock(): bool
    {
        if ($this->writeLockedFilePointer) {
            $bClose = fclose($this->writeLockedFilePointer);
            $this->writeLockedFilePointer = null;
            unlink($this->sLockPath);
            unlink($this->sPidPath);
            return $bClose;
        }
        return true;

    }

    /**
     * Returns Process ID for the lock thread or zero if other case
     * @return int
     */
    public function getLockPid(): int
    {
        if (!$this->isLocked()) {
            return 0;
        }
        $iPid = 0;
        if (is_file($this->sPidPath)) {
            $iPid = file_get_contents($this->sPidPath);
        } elseif ($this->writeLockedFilePointer) {
            $iPid = getmypid();
        }
        return (int)$iPid;
    }

}