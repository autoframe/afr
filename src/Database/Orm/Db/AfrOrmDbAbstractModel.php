<?php

namespace Autoframe\Core\Database\Orm\Db;

use Autoframe\Core\Database\Orm\Cnx\AfrOrmCnxTrait;

/**
 * Base database model to be implemented or extended...
 * abstract class AfrOrmDbAbstractModel implements AfrOrmDbInterface, AfrOrmCnxInterface
 * use AfrOrmCnxTrait, AfrOrmDbTrait, AfrOrmDbMutateTrait;
 */
abstract class AfrOrmDbAbstractModel implements AfrOrmDbInterface
{
    use AfrOrmCnxTrait, AfrOrmDbTrait, AfrOrmDbMutateTrait;
}