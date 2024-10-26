<?php

namespace Autoframe\Core\Database\Orm\Ent;

use Autoframe\Core\Database\Orm\Tbl\AfrOrmTblInterface;

interface AfrOrmEntInterface extends AfrOrmTblInterface
{
    /** @var string Glue string for cache and array index keys */
    const GLUE = '~~';

    public static function _ORM_Ent_Location(): array;
    public static function _ORM_Ent_LocationKey(): string;
    public static function _ORM_Ent_FQCN(): string;
    public static function save(): bool;
    public static function persist(): bool;
    public static function get(): bool;
    public static function hydrate(): bool;

}