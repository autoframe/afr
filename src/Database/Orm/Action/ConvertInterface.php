<?php

namespace Autoframe\Core\Database\Orm\Action;

use Autoframe\Core\Database\Orm\Blueprint\AfrOrmBlueprintInterface;
use Autoframe\Core\Database\Orm\Exception\AfrOrmException;

interface ConvertInterface extends AfrOrmBlueprintInterface #, CnxActionSingletonInterface
{
	/**
	 * @throws AfrOrmException
	 */
	public static function blueprintToTableSql(array $aBlueprint): string;

	public static function encapsulateDbTblColName(string $sDatabaseOrTableName): string;

	public static function encapsulateCellValue($mData);

	/**
	 * @param string $sText
	 * @param string $sQuot
	 * @param int $iStartOffset
	 * @return string[]
	 * @throws AfrOrmException
	 */
	public static function parseExtractQuotedValue(
		string $sText,
		string $sQuot,
		int    $iStartOffset = 0
	): array;

	public static function parseCreateTableBlueprint(string $sTableSql): array;

}