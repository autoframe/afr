<?php

namespace Autoframe\Core\Database\Orm\Action;

interface CnxActionInterfaceDumpable extends CnxActionInterface
{

	//todo implement
	//SET FOREIGN_KEY_CHECKS=0;
	//SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
	//START TRANSACTION;
	//SET time_zone = "+00:00";
	//...
	//SET FOREIGN_KEY_CHECKS=1;
	//COMMIT;


	public function cnxDumpAllDatabasesToLocalDir(
		string $dirPath,
		bool   $individualTableStructures = true,
		bool   $bZipped = true
	): array;

	public function cnxCopyAllDatabasesToCnxAlias(string $sAlias): array;


}