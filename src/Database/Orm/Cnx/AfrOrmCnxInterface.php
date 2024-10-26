<?php

namespace Autoframe\Core\Database\Orm\Cnx;

use Autoframe\Core\Database\Connection\Exception\AfrDatabaseConnectionException;
use Autoframe\Core\Database\Orm\Exception\AfrOrmException;
use PDO;

interface AfrOrmCnxInterface
{
	/** @return string connection manager alias string */
	public static function _ORM_Cnx_Alias(): string;


	/**
	 * @return string get driver string from PDO DSN
	 * Types: mysql, sqlite, pgsql, mssql, cubrid, sybase, dblib, firebird, ibm, informix, oci, odbc
	 * @throws AfrDatabaseConnectionException
	 */
	public static function _ORM_Cnx_Driver(): string;

	/**
	 * @return PDO
	 * @throws AfrDatabaseConnectionException
	 */
	public static function _ORM_Cnx_Pdo(): PDO;

	/**
	 * @return array|null
	 * @throws AfrOrmException
	 */
	public static function _ORM_Cnx_AliasInfo(): ?array;

//    used for driver and pdo methods
//    protected static function _ORM_Cnx_Manager(): AfrDbConnectionManagerInterface;


}