<?php

namespace Autoframe\Core\Database\Connection;

class AfrDbConnectionManagerFacade
{
	protected static ?AfrDbConnectionManagerInterface $oConnectionManager = null;

	/**
	 * @var string|null FQCN class name that implements AfrDbConnectionManagerInterface
	 */
	protected static ?string $sAfrDbConnectionManagerImplementation = null;

	public static function getInstance(): AfrDbConnectionManagerInterface
	{
		if (!empty(static::$sAfrDbConnectionManagerImplementation)) {
			/** @var AfrDbConnectionManagerInterface $sAfrDbConnectionManagerImplementation */
			$sAfrDbConnectionManagerImplementation = static::$sAfrDbConnectionManagerImplementation;
			static::$oConnectionManager = $sAfrDbConnectionManagerImplementation::getInstance();
			static::$sAfrDbConnectionManagerImplementation = null;
		}
		if (!empty(static::$oConnectionManager)) {
			return static::$oConnectionManager;
		}
		if (!empty($_ENV['AfrDbConnectionManagerFacade'])) { //TODO env!!!
			/** @var AfrDbConnectionManagerInterface $sAfrDbConnectionManagerImplementation */
			$sAfrDbConnectionManagerImplementation = $_ENV['AfrDbConnectionManagerFacade'];
			$_ENV['AfrDbConnectionManagerFacade'] = null;
			return static::$oConnectionManager = $sAfrDbConnectionManagerImplementation::getInstance();
		}

		return AfrDbConnectionManagerClass::getInstance();
	}

	public static function setInstance(AfrDbConnectionManagerInterface $connectionManager): AfrDbConnectionManagerInterface
	{
		self::$oConnectionManager = $connectionManager;
		static::$sAfrDbConnectionManagerImplementation = null;
	}

	public static function setConnectionManagerFQCN(string $sAfrDbConnectionManagerImplementation): void
	{
		static::$sAfrDbConnectionManagerImplementation = $sAfrDbConnectionManagerImplementation;
		self::$oConnectionManager = null;
	}

}