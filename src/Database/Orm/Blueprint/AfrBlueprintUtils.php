<?php

namespace Autoframe\Core\Database\Orm\Blueprint;

trait AfrBlueprintUtils
{
	/**
	 * Recursive blueprintMerge
	 * @param array $aOriginal
	 * @param array $aNew
	 * @return array
	 */
	public static function mergeBlueprint(array $aOriginal, array $aNew): array
	{
		$aOriginalKeys = array_keys($aOriginal);
		foreach ($aNew as $sNewKey => $mNewProfile) {
			if (!in_array($sNewKey, $aOriginalKeys) || $aOriginal[$sNewKey] === null) {
				$aOriginal[$sNewKey] = $mNewProfile;
			} elseif (is_array($aOriginal[$sNewKey]) && is_array($mNewProfile)) {
				$aOriginal[$sNewKey] = self::mergeBlueprint($aOriginal[$sNewKey], $mNewProfile);
			} elseif (is_integer($sNewKey)) {
				$aOriginal[] = $mNewProfile;
			} else {
				$aOriginal[$sNewKey] = $mNewProfile;
			}
		}
		return $aOriginal;
	}


	public static function exportArrayAsString(
		array  $aData,
		string $sQuot = "'",
		int    $iTab = 1,
		string $sEndOfLine = "\n",
		string $sPointComa = ';',
		string $sVarName = '$aBlueprint'
	): string
	{
		$sOut = '';
		foreach ($aData as $mk => $mVal) {
			$sKType = gettype($mk);
			$sVType = gettype($mVal);
			$sOut .= str_repeat("\t", $iTab);
			static::exportArrayAsStringFormatKV($sKType, $mk, $sOut, $sQuot);
			$sOut .= '=>';
			if ($sVType === 'array') {
				$sOut .= static::exportArrayAsString($mVal, $sQuot, $iTab + 1, $sEndOfLine, '', '');
			} else {
				static::exportArrayAsStringFormatKV($sVType, $mVal, $sOut, $sQuot);
			}
			$sOut .= ',' . $sEndOfLine;
		}
		if ($sVarName) {
			if (substr($sVarName, 0, 1) !== '$') {
				$sVarName = '$' . $sVarName;
			}
			$sVarName .= '=';
		}
		return str_repeat("\t", max($iTab - 1, 0)) . $sVarName . '[' . $sEndOfLine . $sOut . ']' . $sPointComa . $sEndOfLine;
	}

	/**
	 * @param string $sVType
	 * @param $mVal
	 * @param string $sOut
	 * @param string $sQuot
	 */
	protected static function exportArrayAsStringFormatKV(string $sVType, $mVal, string &$sOut, string $sQuot)
	{
		if ($sVType === 'integer') {
			$sOut .= $mVal;
		} elseif ($sVType === 'boolean') {
			$sOut .= $mVal ? 'true' : 'false';
		} elseif ($sVType === 'double') {
			$mVal = (string)$mVal;
			if (strpos($mVal, '.') === false) {
				$mVal .= '.';
			}
			$sOut .= $mVal;
		} elseif ($sVType === 'NULL') {
			$sOut .= 'NULL';
		} else {
			if ($sVType !== 'string') {
				$mVal = serialize($mVal);
			}
			$sOut .= $sQuot . str_replace(['\\', $sQuot], ['\\\\', '\\' . $sQuot], $mVal) . $sQuot;
		}
	}

}