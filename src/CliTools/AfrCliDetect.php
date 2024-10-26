<?php


namespace Autoframe\Core\CliTools;

/**
 * Class AfrCliDetect
 * Provides methods to detect if the application is being run from the command line interface (CLI) or a web server.
 */
class AfrCliDetect
{
    public static function isCli(): bool
    {
        return http_response_code() === false;
        //return !(strpos(strtolower(php_sapi_name()), 'cli') === false);
    }

    public static function insideCli(): bool
    {
        return static::isCli();
    }

    public static function isWeb(): bool
    {
        return http_response_code() !== false;
    }

    public static function isHttpRequest(): bool
    {
        return static::isWeb();
    }

}