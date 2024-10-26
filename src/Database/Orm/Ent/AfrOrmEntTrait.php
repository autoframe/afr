<?php

namespace Autoframe\Core\Database\Orm\Ent;

trait AfrOrmEntTrait
{
    public static function _ORM_Ent_Location(): array
    {
        return [
            static::_ORM_Cnx_Alias(),
            static::_ORM_Db_Name(),
            static::_ORM_Tbl_Name(),
        ];
    }

    public static function _ORM_Ent_LocationKey(): string
    {
        return implode(static::GLUE, static::_ORM_Ent_Location());
    }


    public static function _ORM_Ent_FQCN(): string
    {
        return static::class;
    }

    //todo load from blueprint or:
    //todo save, persist, interface, etc
}