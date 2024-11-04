<?php

namespace Autoframe\Core\CliTools;

use Autoframe\Core\InterfaceToConcrete\AfrVendorPath;

class AfrVendorDir
{

	public static function pathIsInsideVendorDir(string $sPath): bool
	{
		return AfrVendorPath::pathIsInsideVendorDir($sPath);
	}

	public static function getVendorPath(): string
	{
		return AfrVendorPath::getVendorPath();
	}

	public static function getBaseDirPath(): string
	{
		return AfrVendorPath::getBaseDirPath();
	}

	public static function getComposerJson(): array
	{
		return AfrVendorPath::getComposerJson();
	}

	public static function getComposerTs(): int
	{
		return AfrVendorPath::getComposerTs();
	}


}