<?php

namespace Autoframe\Core\Database\Orm\Tbl;

use Autoframe\Core\Database\Orm\Cnx\AfrOrmCnxTrait;
use Autoframe\Core\Database\Orm\Db\AfrOrmDbMutateTrait;
use Autoframe\Core\Database\Orm\Db\AfrOrmDbTrait;
use Autoframe\Core\Database\Orm\Ent\AfrOrmEntTrait;

/**
 * Base table model to be implemented or extended...
 * abstract class AfrOrmTblAbstractCompleteModel implements DB and HANDLERS
 * use AfrOrmTblTrait, AfrOrmEntTrait;
 */
abstract class AfrOrmTblAbstractCompleteModel implements AfrOrmTblInterface
{
    use AfrOrmTblTrait, AfrOrmEntTrait, AfrOrmCnxTrait, AfrOrmDbTrait, AfrOrmDbMutateTrait;
}