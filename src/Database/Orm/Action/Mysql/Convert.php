<?php
declare(strict_types=1);

namespace Autoframe\Core\Database\Orm\Action\Mysql;

use Autoframe\Core\Database\Orm\Action\CnxActionSingletonTrait;
use Autoframe\Core\Database\Orm\Action\ConvertInterface;
use Autoframe\Core\Database\Orm\Blueprint\AfrOrmBlueprintInterface;

/**
 * Know how PIVOT tables and relationships:
 *
 * Information on the tables can be concatenated as a comment into the table definition / column definition
 * This is usefully when migrating an existing database structure from a source, and generate a migration class
 * When a class migration is defined and called, the comments are also pushed as table alters
 * The table serialized blueprint info will always be overwritten in the upper cases
 * The table serialized blueprint info will be merged with any local config
 *
 * 1-1 relationships can be defined for Countries and Capital city as int foreign keys between each table
 * 1-1 Eg
 *      [Country.capital_id has UNIQUE Foreign Key Capital.id] and reverse
 *      [Capital.country_id has UNIQUE Foreign Key Country.id]
 *      If the unique property is not set, then the table can easily morph into 1-N or N-1
 *
 * (1-N / N-1) for a web shop for Products manufactured only by a single Brand. The shop can have may Brands.
 * 1-N  [Brand.id has Foreign Key Product.brand_id] a brand can manufacture many products
 * N-1  [Product.brand_id has Foreign Key Brand.id] products can be manufactured by a single brand
 *
 * N-N for web shop for Products that can belong to one or many Categories using PIVOT Table
 *      [Product.id and Category.id] are a part of a PIVOT Table
 *
 *      The table is naming is made as following: implode('_',natsort([t2,t1])).'_pivot' => t1_t2_pivot
 *      The pivot table will be sored in the connection.db of the first alphabetical table from natsort, meaning
 *      natsort([t2,t1])[0] => is t1, so we get the db1 and Pdo1 connection and create the table Pdo1.db1.t1_t2_pivot
 *
 *      Avoid making pivots between Tables having the same names in two different databases, especially for sqlite
 *
 *      Is mandatory to have stored into t2 Blueprint some information regarding t1 and the same way around
 *      In t1 Blueprint, if the information is partially defined, we try to determine it, using this priority logic:
 *          - t2 table name absolutely mandatory (we can try to read it from MySQL foreign key info)
 *          - t2 database name (if empty, we default to t1 database name and pull t1 PDO)
 *          - t2 pdo for databases having the same name and from other connections
 *              Eg1. backup from host A to B
 *              Eg2. sqlite databases don't have a database name, only a filename, so the difference is made from PDO
 *
 * Lazy load columns:
 *      They are defined in the table comment. By default, blob is lazy loaded be default
 *      You can set lazy load to false in the comments or blueprint or on the entity
 * Columns can be readonly (not updatable) when updating
 */
class Convert implements ConvertInterface
{
	use Syntax;
	use EscapeTrait;
	use SqlToBp;
	use BpToSql;
	use CnxActionSingletonTrait;
}

