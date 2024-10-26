<?php

namespace Autoframe\Core\SocketCache\LaravelPort\Contracts\Redis;

interface Factory
{
    /**
     * Get a Redis connection by name.
     *
     * @param  string|null  $name
     * @return \Autoframe\Core\SocketCache\LaravelPort\Redis\Connections\Connection
     */
    public function connection($name = null);
}
