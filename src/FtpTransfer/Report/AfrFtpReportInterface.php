<?php

namespace Autoframe\Core\FtpTransfer\Report;

use Autoframe\Core\Exception\AfrException;
use Autoframe\Core\FtpTransfer\AfrFtpBackupConfig;

interface AfrFtpReportInterface
{
    /**
     * @param AfrFtpBackupConfig $oFtpConfig
     * @return array
     * @throws AfrException
     */
    public function ftpReport(AfrFtpBackupConfig $oFtpConfig): array;
}