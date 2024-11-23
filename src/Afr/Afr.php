<?php

namespace Autoframe\Core\Afr;

use Autoframe\Core\Env\AfrEnv;
use Autoframe\Core\Env\AfrEnvInterface;
use Autoframe\Core\Exception\AfrException;
use Autoframe\Core\Container\AfrContainerInterface;
use Autoframe\Core\Container\AfrLiteContainer;
use Autoframe\Core\Tenant\AfrTenant;



class Afr
{
	protected static Afr $oAfr;
	protected string $sAppBaseDirectory;
	protected string $sContainerClass = AfrLiteContainer::class;
	protected AfrContainerInterface $oAfrContainer;
	protected AfrEnv $oAfrEnv;

	/**
	 * @throws AfrException
	 */
	public function __construct(
		string $sAppBaseDirectory = null,
		string $sContainerClass = null
	)
	{

		if (!empty(static::$oAfr)) {
			throw new AfrException('Afr already initialized!');
		}
		static::$oAfr = $this;

		if (empty($this->sAppBaseDirectory = $sAppBaseDirectory ?: (defined($c = '\AFR_BASE_DIR') ? constant($c) : ''))) {
			throw new AfrException('App directory not set!');
		} else {
			AfrTenant::setBaseDirPath($this->sAppBaseDirectory);
			($this->oAfrEnv = AfrEnv::getInstance())->setBaseDir($this->sAppBaseDirectory);
		}
		if ($sContainerClass) { //lazy assign without checking if instance of AfrContainerInterface
			$this->sContainerClass = $sContainerClass;
		} elseif (defined('\AFR_CONTAINER')) {
			$this->sContainerClass = constant('\AFR_CONTAINER');
		}
		$this->oAfrContainer = $this->sContainerClass::getInstance();
	}

	public function getAppBaseDirectory(): string
	{
		return $this->sAppBaseDirectory;
	}

	public static function app(): ?self
	{
		return static::$oAfr ?? null;
	}

	/**
	 * @return AfrExecutionThread
	 */
	public function thread(): AfrExecutionThread
	{
		return AfrExecutionThread::getInstance();
	}

	public function container(): AfrContainerInterface
	{
		return $this->oAfrContainer;
	}

	public function env(): AfrEnvInterface
	{
		return $this->oAfrEnv;
	}

	/**
	 */
	public function run(...$mArgs): array
	{
		return $this->thread()->run(...$mArgs);
	}

}