<?php

namespace Autoframe\Core\Router;

use Autoframe\Core\CliTools\AfrCliPromptMenu;
use Autoframe\Core\CliTools\AfrCliTextColors;

class AfrCliRouterHelper
{


	public static function cliQA(array $aQuestionClosure)
	{
		$aOptions = array_merge(['Return' => null,], $aQuestionClosure);
		$chosenKey = AfrCliPromptMenu::promptMenu(
			"What to execute?",
			array_keys($aOptions),
			array_key_first($aOptions)
		);
		if (is_callable($aOptions[$chosenKey])) {
			$r = $aOptions[$chosenKey]();
			$oTxt = AfrCliTextColors::getInstance()->textAppend("\n\t");
			if ($r === true) {
				$oTxt->colorGreen("Success: $chosenKey");
			} elseif ($r === false) {
				$oTxt->colorRed("Error: $chosenKey");
			} elseif (is_array($r)) {
				$oTxt->styleItalic(true)->textAppend(implode("\n",$r))->styleItalic(false);
			} elseif (!empty((string)$r)) {
				$oTxt->styleItalic(true)->textAppend((string)$r)->styleItalic(false);
			}
			$oTxt->styleDefaultAllBgColor()->textAppend("\n")->textPrint();
			return $r;
		}
		if ($aOptions[$chosenKey] === null) {
			return null;
		}
		return false;
	}

	public static function cliItalicGrey(string $sTxt): string
	{
		return AfrCliTextColors::getInstance()
			->styleItalic(true)
			->colorGrayDark($sTxt)
			->styleDefaultAllBgColor()
			->textGet();
	}

}