<?php

namespace Autoframe\Core\Container\Exception;

use Autoframe\Core\Exception\AfrException;
use Psr\Container\ContainerExceptionInterface;

class CircularDependencyException extends AfrException implements ContainerExceptionInterface
{
    //
}
