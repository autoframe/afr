<?php

namespace Autoframe\Core\App;

use Autoframe\Core\Exception\AfrException;
use Autoframe\Core\Tenant\AfrTenant;
use Autoframe\Core\Env\AfrEnv;

$_SERVER['REQUEST_TIME_FLOAT'] ??= microtime(true);

class AfrBaseIndex
{
	const BASE_INIT = 'baseInit';
	const SET_CONSTANTS = 'setConstants';
	const TENANT_SET_PATH = 'tenantSetPath';
	const TENANT_LOAD = 'tenantLoad';
	const TENANT_EXTRA_CONFIG = 'tenantExtraConfig';
	const ENV_SET_PATH = 'envSetPath';
	const ENV_LOAD = 'envLoad';
	const ENV_EXTRA_CONFIG = 'envExtraConfig';
	const MODULE_READ = 'moduleRead';
	const APP_INIT = 'appInit';
	const ROUTER_INIT = 'routerInit';
	const ROUTE_HANDEL = 'routeHandel';
	const VIEW_RENDER = 'viewRender';
	const SHUTDOWN_FX = 'shutdownFx';

	const ORDER = [
		self::SET_CONSTANTS,
		self::BASE_INIT,
		self::TENANT_SET_PATH,
		self::ENV_SET_PATH,
		self::TENANT_LOAD,
		self::TENANT_EXTRA_CONFIG,
		self::ENV_LOAD,
		self::ENV_EXTRA_CONFIG,
		self::MODULE_READ,
		self::APP_INIT,
		self::ROUTER_INIT,
		self::ROUTE_HANDEL,
		self::VIEW_RENDER,
		self::SHUTDOWN_FX,
	];

	protected static array $aExecuteBeforeStep = [];
	protected static array $aOverwriteStep = [];
	protected static array $aExecuteAfterStep = [];
	protected static array $aCustomOrder = [];
	protected static array $aReport = [];

	protected array $aStep = []; //from populateSteps()
	protected string $sBaseDirPath;


	/**
	 * @param string $sBaseDirPath
	 * @throws AfrException
	 */
	public function __construct(string $sBaseDirPath)
	{
		$this->sBaseDirPath = $sBaseDirPath;
		if (empty($this->sBaseDirPath)) {
			throw new AfrException('Unable to configure the base directory!');
		}
		static::$aReport = [];
		$this->populateSteps();
	}


	protected function populateSteps(): void
	{
		$this->aStep[self::SET_CONSTANTS] = function () {
			if (is_file($sConstantsPath = $this->sBaseDirPath . DIRECTORY_SEPARATOR . 'constants.php')) {
				include_once $sConstantsPath;
			}
		};

		$this->aStep[self::TENANT_SET_PATH] = function () {
			AfrTenant::setBaseDirPath($this->sBaseDirPath);
		};

		$this->aStep[self::ENV_SET_PATH] = function () {
			AfrEnv::getInstance()->setBaseDir($this->sBaseDirPath);
		};

		$this->aStep[self::TENANT_LOAD] = function () {
			AfrTenant::loadConfig(); //loads: sBaseDirPath/tenant.env.php
		};

		$this->aStep[self::ENV_LOAD] = function () {
			$oEnv = AfrEnv::getInstance();
			// Run $oEnv->setBaseDir(__DIR__)->readEnv() or $oEnv->readEnvPhpFile(path)
			//	$oEnv->readEnv(0); //load env files from __DIR__ without cache
			$oEnv->readEnv(
				3600*24*30,
				[
					$this->sBaseDirPath .
					DIRECTORY_SEPARATOR .
					AfrTenant::getTenantAlias() .
					'.' . $_ENV['AFR_ENV'] . '.env'
				]
			); //cache loaded env file for 60 seconds
		//	$oEnv->setInlineEnvVar('FOO', 'BAR'); //set *[FOO]=BAR

			$oEnv->getEnv('APP_ENV'); //get env key
			$oEnv->getEnv(); //get all env keys as array

			$oEnv->registerEnv($bMutableOverwrite = true, $bRegisterPutEnv = true);
			// populate $_SERVER, $_ENV and getenv()
		};


	}


	public function run(): array
	{
		foreach (static::customOrder() as $sStepName) {
			if (!empty(static::$aExecuteBeforeStep[$sStepName])) {
				foreach (static::$aExecuteBeforeStep[$sStepName] as $i => $closure) {
					static::$aReport[$sStepName . '@b' . $i] = $closure();
				}
			}
			if (!empty(static::$aOverwriteStep[$sStepName])) {
				static::$aReport[$sStepName . '@o'] = static::$aOverwriteStep[$sStepName]();
			} elseif (!empty($this->aStep[$sStepName])) {
				static::$aReport[$sStepName . '@s'] = $this->aStep[$sStepName]();
			}
			if (!empty(static::$aExecuteAfterStep[$sStepName])) {
				foreach (static::$aExecuteAfterStep[$sStepName] as $i => $closure) {
					static::$aReport[$sStepName . '@a' . $i] = $closure();
				}
			}
		}
		return static::$aReport;
	}

	public static function customOrder(array $aCustomOrder = []): array
	{
		if (!empty($aCustomOrder)) {
			static::$aCustomOrder = $aCustomOrder;
		} elseif (empty(static::$aCustomOrder)) {
			static::$aCustomOrder = static::ORDER;
		}
		return static::$aCustomOrder;
	}

	public static function executeBeforeStep(string $sStep, \Closure $closure)
	{
		static::$aExecuteBeforeStep[$sStep][] = $closure;
	}

	public static function overwriteStep(string $sStep, \Closure $closure)
	{
		static::$aOverwriteStep[$sStep] = $closure;
	}


	public static function executeAfterStep(string $sStep, \Closure $closure)
	{
		static::$aExecuteAfterStep[$sStep][] = $closure;
	}

	public static function getReport(): array
	{
		return [
			static::$aExecuteBeforeStep,
			static::$aOverwriteStep,
			static::$aExecuteAfterStep,
			static::customOrder(),
			static::$aReport,
		];
	}

}