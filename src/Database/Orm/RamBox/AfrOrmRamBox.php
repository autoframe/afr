<?php

namespace Autoframe\Core\Database\Orm\RamBox;

class AfrOrmRamBox
{
    // TODO set timezone verificare pe sesiune / server
    /**
     * MODEL:
     * [connectionHost&port + hash] cum tratez alias daca este din cluster?
     * [dbName] => [ tables=>[...], dbProps=>[collation....]]  numele gol devine ceva?
     *         [tableName] => [map; tableDescription/Props[PrimaryKey, other keys, collation...]; rows]
     *              rows[primaryKey]=> [column  => value,...]
     */
    /**
     * FLAGS:
     * dirty
     * hydrated
     * default
     * fromDb
     * fromCache
     */
    /**
     * @var array
     */
    protected static array $aRamBox = [];




}