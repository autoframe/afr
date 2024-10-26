<?php
declare(strict_types=1);

namespace Autoframe\Core\Arr\Export;

trait AfrArrExportArrayAsStringTrait
{
    /**
     * @param array $aData
     * @param string $sQuot
     * @param string $sEndOfLine
     * @param string $sPointComa
     * @param string $sVarName
     * @return string
     */
    public function exportPhpArrayAsString(
        array  $aData,
        string $sQuot = "'",
        string $sEndOfLine = "\n",
        string $sPointComa = ';',
        string $sVarName = '$aData'
    ): string
    {
        $sOut = '';
        foreach ($aData as $mk => $mVal) {
            $sKType = gettype($mk);
            $sVType = gettype($mVal);
            $this->exportPhpArrayAsStringFormatKV($sKType, $mk, $sOut, $sQuot);
            $sOut .= '=>';
            if ($sVType === 'array') {
                $sOut .= $this->exportPhpArrayAsString($mVal, $sQuot, $sEndOfLine, '', '');
            } else {
                $this->exportPhpArrayAsStringFormatKV($sVType, $mVal, $sOut, $sQuot);
            }
            $sOut .= ',' . $sEndOfLine;
        }
        if ($sVarName) {
            if (substr($sVarName, 0, 1) !== '$') {
                $sVarName = '$' . $sVarName;
            }
            $sVarName .= '=';
        }
        return $sVarName . '[' . $sEndOfLine . $sOut . ']' . $sPointComa . $sEndOfLine;
    }

    /**
     * @param string $sVType
     * @param $mVal
     * @param string $sOut
     * @param string $sQuot
     */
    private function exportPhpArrayAsStringFormatKV(string $sVType, $mVal, string &$sOut, string $sQuot)
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
            $sOut .= $sQuot . $this->exportPhpArrayAsStringAddSlashes($mVal, $sQuot) . $sQuot;
        }
    }

    /**
     * @param string $mVal
     * @param string $sQuot
     * @return string
     */
    private function exportPhpArrayAsStringAddSlashes(string $mVal, string $sQuot): string
    {
        $s = '\\';
        return str_replace([$s, $sQuot], [$s . $s, $s . $sQuot], $mVal);
    }

}