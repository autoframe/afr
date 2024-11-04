<?php
declare(strict_types=1);

namespace Unit\Env;

use Autoframe\Core\Env\AfrEnv;

use Autoframe\Core\Env\Exception\AfrEnvException;
use PHPUnit\Framework\TestCase;

class AfrEnvEmptyTest extends TestCase
{

    /**
     * @test
     */
    public function AfrEnvEmptyTest(): void
    {
        $oEnv = AfrEnv::getInstance();
        $this->assertSame(null,  $oEnv->getEnv('APP_ENV'));
        $this->assertSame(true,  $oEnv->isDev());

        $oEnv->setInlineEnvVar('APP_ENV', 'STAGING');
        $this->assertSame('STAGING',  $oEnv->getEnv('APP_ENV'));
    }


}