<?php

namespace Autoframe\Core\Database\Orm\Tbl;

use Autoframe\Core\Database\Orm\Db\AfrOrmDbInterface;
use Autoframe\Core\Database\Orm\Ent\AfrOrmEntInterface;

interface AfrOrmTblInterface extends AfrOrmDbInterface,AfrOrmEntInterface
{
    /** @return string Table name */
    public static function _ORM_Tbl_Name(): string;
}