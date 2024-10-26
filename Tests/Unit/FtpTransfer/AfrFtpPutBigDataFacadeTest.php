<?php
declare(strict_types=1);

namespace Unit\FtpTransfer;

use Autoframe\Core\FtpTransfer\AfrFtpBackupConfig;
use Autoframe\Core\FtpTransfer\FtpBusinessLogic\AfrFtpPutBigDataFacade;
use Autoframe\Core\FtpTransfer\Report\AfrFtpReportBpg;
use Autoframe\Core\FileSystem\Versioning\AfrDirMaxFileMtimeClass;
use Autoframe\Core\FtpTransfer\FtpBusinessLogic\AfrFtpNbrCopiesDms;
use Autoframe\Core\FtpTransfer\Log\AfrFtpLogInline;
use Autoframe\Core\ProcessControl\Lock\AfrLockFileClass;
use PHPUnit\Framework\TestCase;

class AfrFtpPutBigDataFacadeTest extends TestCase
{

	public static function insideProductionVendorDir(): bool
    {
        return strpos(__DIR__, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false;
    }

    protected function setUp(): void
    {
        $this->oFtpConfig = new AfrFtpBackupConfig();
    }
	
	protected function tearDown(): void
    {
        //cleanup between tests for static
    }

    /**
     * @test
     */
    public function AfrFtpPutBigDataFacadeTest(): void
    {
        $oAfrFtpPutBigDataFacade = new AfrFtpPutBigDataFacade($this->oFtpConfig);
        $this->assertSame($oAfrFtpPutBigDataFacade instanceof AfrFtpPutBigDataFacade, true);
    }


}