<?php

namespace Autoframe\Core\Database\Orm;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;

//sql_mode=NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION

//SET @@sql_mode := REPLACE(@@sql_mode, 'NO_ZERO_DATE', '');
//SET GLOBAL sql_mode = 'modes';
//SET SESSION sql_mode = 'modes';
#init-connect=\'SET NAMES utf8\'
#collation_server=utf8_unicode_ci
#character_set_server=utf8
#character-set-server=utf8mb4
#collation-server=utf8mb4_general_ci

#[Entity]
#[Table('invoices')]
class AfrTableCore //active record pattern strategy https://www.youtube.com/watch?v=ZRdgVuIppYQ&ab_channel=ProgramWithGio
{
    //todo query builder: https://www.sitepoint.com/getting-started-fluentpdo/  !!!!!!
    //todo query builder: https://dev.to/mvinhas/write-your-first-querybuilder-with-pdo-and-php-o8h

    protected static string $sTableName;
    protected static string $sDbAlias; // nickname pointing to the db and connection
    //todo get from db class
    protected static string $sDbName; //todo FROM DB ALIAS? ; blank for xls and sqlite or same name for different connections
    protected static string $sConnAlias; //todo on database instead on database name conflicts over clone connections / hosts



    protected static array $aMap = []; //column_name -> columnName
    protected static array $aDataType = []; //int, str, datetime...
    protected static array $aDataTypeRequired = [];
    protected static array $aTblStructure = []; //AI, onupdate, timezone?

    protected static array $aRelationsOneManyForeignKeys = []; //todo https://prnt.sc/979X7w_XzUpS



    //todo test make separate row class / structure
    protected static bool $bDirtyRow;
    protected static array $aRows = [];
    protected static array $aRowsInsert = []; //without id
    protected static array $aRowsDeleted = []; //without id //todo keep or unset
    protected static array $aRowsDirty = []; //todo dirty
    protected static array $aPartialHydrate = []; //todo !dirty ??

    protected static array $aEvents = []; //todo  https://prnt.sc/-b3n8oJWPU-o  GIO!

    #[Id]
    #[Column, GeneratedValue]
    private int $id;
    #[Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private float $amount;

}
/*
use firma_X3SH;
SET timestamp=1714039400;
SELECT * FROM `operator`.`cli_clienti` WHERE ( ( ( `id_str_client` IN ('X3' ) ) ) );
 * */