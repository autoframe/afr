<?php

/** 
 * The logging class is the most known and praised use of the Singleton pattern.
 * In most cases, you need a single logging object that writes to a single log
 * file (control over shared resource). You also need a convenient way to access
 * that instance from any context of your app (global access point).
 */
class thfLogger extends thfSingleton
{
    /**
     * A file pointer resource of the log file.
     */
    private $fileHandle;

    /**
     * Since the Singleton's constructor is called only once, just a single file
     * resource is opened at all times.
     *
     * Note, for the sake of simplicity, we open the console stream instead of
     * the actual file here.
     */
    protected function __construct()
    {
        //$this->fileHandle = fopen('php://stdout', 'w');
        $this->fileHandle = fopen('./log.txt', 'w');
    }

    /**
     * Write a log entry to the opened file resource.
     */
    public function writeLog($message)
    {
        $date = date('Y-m-d');
        fwrite($this->fileHandle, "$date: $message\n");
    }

    /**
     * Just a handy shortcut to reduce the amount of code needed to log messages
     * from the client code.
     */
    public static function log($message)
    {
        $logger = static::getInstance();
        $logger->writeLog($message);
    }
}
