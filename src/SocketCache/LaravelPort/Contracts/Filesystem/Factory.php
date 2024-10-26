<?php

namespace Autoframe\Core\SocketCache\LaravelPort\Contracts\Filesystem;

interface Factory
{
    /**
     * Get a filesystem implementation.
     *
     * @param  string|null  $name
     * @return \Autoframe\Core\SocketCache\LaravelPort\Contracts\Filesystem\Filesystem
     */
    public function disk($name = null);
}
