<?php

namespace Autoframe\Core\Database\Orm\Tbl;

use Autoframe\Core\Database\Orm\Db\AfrOrmDbAbstractModel;
use Autoframe\Core\Database\Orm\Ent\AfrOrmEntTrait;

/**
 * Base table model to be implemented or extended...
 * abstract class AfrOrmTblAbstractModel extends AfrOrmDbAbstractModel implements AfrOrmTblInterface
 * use AfrOrmTblTrait, AfrOrmEntTrait;
 */
abstract class AfrOrmTblAbstractModel extends AfrOrmDbAbstractModel implements AfrOrmTblInterface
{
    use AfrOrmTblTrait, AfrOrmEntTrait;
}