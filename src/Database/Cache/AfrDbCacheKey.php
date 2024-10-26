<?php

namespace Autoframe\Core\Database\Cache;

class AfrDbCacheKey
{
	const GLUE = '~~';
	protected ?string $sCnxAlias = null;
	protected ?string $sDbName = null;
	protected ?string $sTableName = null;
	protected string $sFunction;
	protected array $aParams;

	public function __construct(
		string $sFunction,
		array  $aParams,
		string $sCnxAlias = null,
		string $sDbName = null,
		string $sTableName = null
	)
	{
		$this->sCnxAlias = $sCnxAlias;
		$this->sDbName = $sDbName;
		$this->sTableName = $sTableName;
		$this->sFunction = $sFunction;
		$this->aParams = $aParams;
	}

	public function getCnxAlias(): ?string
	{
		return $this->sCnxAlias;
	}

	public function ignoreCnxAlias(): self
	{
		$this->sCnxAlias = null;
		return $this;
	}

	public function getDbName(): ?string
	{
		return $this->sDbName;
	}

	public function ignoreDbName(): self
	{
		$this->sDbName = null;
		return $this;
	}

	public function getTableName(): ?string
	{
		return $this->sTableName;
	}

	public function ignoreTableName(): self
	{
		$this->sTableName = null;
		return $this;
	}

	public function getFunction(): string
	{
		return $this->sFunction;
	}

	public function getParams(): array
	{
		return $this->aParams;
	}

	public function ignoreParams(): self
	{
		$this->aParams = [];
		return $this;
	}


	public function __toString()
	{
		return
			implode(
				self::GLUE,
				[
					$this->cleanupFilename($this->getCnxAlias()),
					$this->cleanupFilename($this->getDbName()),
					$this->cleanupFilename($this->getTableName()),
					$this->cleanupFilename($this->getFunction()),
					md5(serialize($this->getParams()))
				]
			);
	}

	/**
	 * @param string|null $filename
	 * @return string
	 */
	public function cleanupFilename(?string $filename = null): string
	{
		return preg_replace('/[^a-zA-Z0-9_]/', '_', (string)$filename);
	}


}