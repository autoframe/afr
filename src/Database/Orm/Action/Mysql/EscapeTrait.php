<?php

namespace Autoframe\Core\Database\Orm\Action\Mysql;


trait EscapeTrait
{
	public static function encapsulateDbTblColName(string $sDatabaseOrTableName): string
	{
		return '`' . str_replace(['`', '\\'], ['``', '\\\\'], $sDatabaseOrTableName) . '`';
	}


	public static function encapsulateCellValue($mData, bool $bForceEncapsulateAsString = false)
	{
		//todo ints, floats, NULL as null, etc
		if ($mData === null) {
			return $bForceEncapsulateAsString ? "''" : 'NULL';
		} elseif (is_integer($mData) || is_float($mData)) {
			return $bForceEncapsulateAsString ? "'$mData'" : $mData;
		} elseif (is_bool($mData)) {
			$mData = $mData ? 1 : 0;
			return $bForceEncapsulateAsString ? "'$mData'" : $mData;
		} elseif (is_array($mData)) { //todo: enum/set INSERT INTO `t` (`set_max_64_vals`) VALUES ( 'a\'\"b,x`x');
			$mData = implode(',', str_replace(',', ';', $mData)); //#1367 - Illegal set 'comma,comma' value found during parsing
		} elseif (is_object($mData)) { //todo verifica daca exista metoda to string
			$mData = method_exists($mData, '__toString') ? (string)$mData : json_encode($mData);
		}
		return "'" . str_replace(["'", '\\'], ["''", '\\\\'], (string)$mData) . "'";
	}

	public static function q($mData, bool $bForceEncapsulateAsString = false)
	{
		return static::encapsulateCellValue($mData, $bForceEncapsulateAsString);
	}


	public function escapeDbName(string $sDatabaseName): string
	{
		return static::encapsulateDbTblColName($sDatabaseName);
	}

	public function escapeTableName($sTableName): string
	{
		return static::encapsulateDbTblColName($sTableName);
	}

	public function escapeColumnName($sColumnName): string
	{
		return static::encapsulateDbTblColName($sColumnName);
	}


	public function escapeValueAsMixed($mValue)
	{
		return static::encapsulateCellValue($mValue, false);
	}

	public function escapeValueAsString($mValue): string
	{
		return static::encapsulateCellValue($mValue, true);
	}

}