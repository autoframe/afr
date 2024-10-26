<?php
declare(strict_types=1);

namespace Autoframe\Core\AW\Exception;

use Autoframe\Core\Exception\AfrException;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends AfrException implements NotFoundExceptionInterface
{

}