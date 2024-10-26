<?php

namespace Autoframe\Core\Database\Orm\Action;

interface EscapeInterface
{
	public function escapeDbName(string $sDatabaseName): string;

	public function escapeTableName(string $sTableName): string;

	public function escapeColumnName(string $sColumnName): string;

	public function escapeValueAsMixed($mValue);

	public function escapeValueAsString($mValue): string;

}