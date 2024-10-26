<?php
declare(strict_types=1);

namespace Autoframe\Core\SocketCache\Integrity;

use Autoframe\Core\SocketCache\AfrCacheSocketConfig;

interface AfrSocketIntegrityInterface
{
    /**
     * @param AfrCacheSocketConfig $oConfig
     */
    public function __construct(AfrCacheSocketConfig $oConfig);

    /**
     * @param string $sRawRead
     * @return array
     */
    public function svDecodeRead(string $sRawRead): array;

    /**
     * @param string $sWrite
     * @return string
     */
    public function svCodeWrite(string $sWrite): string;

    /**
     * @param string $sRawRead
     * @return array
     */
    public function clDecodeRead(string $sRawRead): array;

    /**
     * @param string $sWrite
     * @return string
     */
    public function clCodeWrite(string $sWrite): string;
}