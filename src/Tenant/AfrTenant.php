<?php

namespace Autoframe\Core\Tenant;

use Autoframe\Core\CliTools\AfrCliPromptMenu;
use Autoframe\Core\Exception\AfrException;
use Autoframe\Core\InterfaceToConcrete\AfrToConcreteStrategiesClass;

/**
 * Class AfrTenant
 *
 * This class manages configuration settings and processes for an application that supports multiple tenants.
 *
 * @package YourPackage
 */
class AfrTenant
{

	protected static array $aTenantCfgIns = [];

	public string $sTenantAlias;
	public string $sRoot;
	public bool $bDebug;
	public string $sEnv;
	public string $sTmpDir;
	public string $sHtmlDir;
	public bool $bAssetDirMapAny;
	public string $sAssetsDir;
	public array $aProtocolDomain = [];
	protected array $aAssetsExtraDirs;


	/**
	 * @throws AfrException
	 */
	public function __construct(string $sTenantAlias)
	{
		$sTenantAlias = preg_replace('/[^ \w]+/', '_', $sTenantAlias);
		if (empty($sTenantAlias)) {
			throw new AfrException("Tenant '$sTenantAlias' can't be empty!");
		} elseif (isset(self::$aTenantCfgIns[$sTenantAlias])) {
			throw new AfrException("Tenant '$sTenantAlias' is already defined!");
		}
		$this->sTenantAlias = $sTenantAlias;
	}

	public function setRoot(string $ROOT = '/'): self
	{
		$this->sRoot = $ROOT;
		return $this;
	}


	public function setEnv(string $AFR_ENV = null): self
	{
		if ($AFR_ENV === null) {
			$AFR_ENV = $_ENV['AFR_ENV'] ?? getenv('AFR_ENV') ?: 'dev';
		}
		$this->sEnv = strtoupper((string)$AFR_ENV);
		return $this;
	}

	public function setTempDir(string $TMP_DIR = null, bool $bSystemTemp = false): self
	{
		if (empty($TMP_DIR)) {
			$TMP_DIR = ($bSystemTemp ? sys_get_temp_dir() : self::getBaseDirPath()) .
				DIRECTORY_SEPARATOR . 'AfrTemp';
		}
		$this->sTmpDir = (string)$TMP_DIR;
		return $this;
	}

	public function setHtmlDir(string $HTML_DIR = null): self
	{
		if (empty($HTML_DIR)) {
			$HTML_DIR = self::getBaseDirPath() . DIRECTORY_SEPARATOR . 'public_html' .
				(count(self::$aTenantCfgIns) ? '_' . $this->sTenantAlias : '');
		}
		$this->sHtmlDir = (string)$HTML_DIR;
		return $this;
	}

	public function setAssetsDir(string $ASSETS_DIR = null, array $aMapExtra = [], bool $bMapAnyAsset = false): self
	{
		if (empty($ASSETS_DIR)) {
			$ASSETS_DIR = self::getBaseDirPath() . DIRECTORY_SEPARATOR . 'public_assets' .
				DIRECTORY_SEPARATOR . $this->sTenantAlias;
		}
		$this->bAssetDirMapAny = $bMapAnyAsset;
		$this->sAssetsDir = $ASSETS_DIR;

		$this->aAssetsExtraDirs = [];
		foreach (array_merge(['img', 'css', 'js', 'media', 'data'], $aMapExtra) as $sAsset) {
			$this->aAssetsExtraDirs[$sAsset] = $ASSETS_DIR . DIRECTORY_SEPARATOR . $sAsset;
		}

		return $this;
	}


	public function setDebug(bool $AFR_DEBUG = null): self
	{
		if ($AFR_DEBUG === null) {
			$AFR_DEBUG = self::isCli() ? (
				$_ENV['AFR_DEBUG'] ??
				getopt('', ['debug:'])['debug'] ??
				getopt('', ['debug::'])['debug'] ??
				getenv('AFR_DEBUG')
			) : (
				!empty($_COOKIE[md5(__FILE__)]) ||
				($_ENV['AFR_DEBUG'] ?? getenv('AFR_DEBUG'))
			);
		}
		$this->bDebug = (bool)$AFR_DEBUG;
		return $this;
	}


	public function setProtocolDomainName(array $aProtocolDomain = ['http://app.test', 'http://localhost:8088', 'http://127.0.0.1']): self
	{
		$this->aProtocolDomain = $aProtocolDomain;
		return $this;
	}

	public function autoSetupAndPushTenantConfig(): self
	{
		($this->sEnv ?? $this->setEnv());
		($this->bDebug ?? $this->setDebug());
		(empty($this->aProtocolDomain) ? $this->setProtocolDomainName() : false);
		(empty($this->sRoot) ? $this->setRoot() : false);
		(empty($this->sTmpDir) ? $this->setTempDir() : false);
		(empty($this->sHtmlDir) ? $this->setHtmlDir() : false);
		(empty($this->aAssetsExtraDirs) ? $this->setAssetsDir() : false);
		return self::$aTenantCfgIns[$this->sTenantAlias] = $this;
	}

	public static function isCli(): bool
	{
		if (!isset(self::$bIsCli)) {
			self::$bIsCli = http_response_code() === false;
		}
		return self::$bIsCli;
	}

	public static function getTenantEnvFilePath(): string { return self::$sTenantEnvFilePath; }

	public static function getProtocolDomain(): string { return self::$sProtocolDomain; }

	public static function getBaseDirPath(): ?string { return self::$sBaseDirPath ?? null; }

	public static function getTenantAlias(): ?string { return self::$sAppTenantAlias ?? null; }

	public static function getTenantModuleConfigFilePath(): string { return self::$sTenantModuleConfigFilePath; }

	public static function getTenantRoutesFilePath(): string { return self::$sTenantRoutesFilePath; }
	public static function getTenantToConcreteStrategiesFilePath(): string { return self::$sToConcreteStrategiesFilePath; }

	public static function getPublicHtmlDir(): string { return self::$sPublicHtmlDir; }

	public static function getPublicAssetsPath(): string { return self::$sPublicAssetsPath; }

	public static function getPublicAssetsDirs(): array { return self::$aPublicAssetsDirs; }

	public static function getPublicAssetsDirCssWeb(): string { return self::$sPublicAssetsDirCssWeb; }

	public static function getPublicAssetsDirJsWeb(): string { return self::$sPublicAssetsDirJsWeb; }

	public static function getPublicAssetsDirImgWeb(): string { return self::$sPublicAssetsDirImgWeb; }

	public static function getPublicAssetsDirMediaWeb(): string { return self::$sPublicAssetsDirMediaWeb; }

	public static function getPublicAssetsDirDataWeb(): string { return self::$sPublicAssetsDirDataWeb; }

	public static function getStorageDir(): string { return self::$sStorageDir . DIRECTORY_SEPARATOR . self::getTenantAlias(); }

	public static function getTempDir(): string
	{
		if (!isset(self::$sTempDir)) {
			return sys_get_temp_dir();
		}
		return self::$sTempDir . DIRECTORY_SEPARATOR . self::getTenantAlias();
	}

	public static function getWebRoot(): string { return self::$sWebRoot; }

	public static function getAllTenants(): array { return self::$aTenantCfgIns; }

	protected static bool $bIsCli;
	protected static string $sTenantEnvFilePath;
	protected static string $sTenantModuleConfigFilePath;
	protected static string $sTenantRoutesFilePath;
	protected static ?string $sToConcreteStrategiesFilePath = null; // php file having a closure(AfrToConcreteStrategiesInterface)
	protected static string $sProtocolDomain;
	protected static string $sWebRoot = '/';

	protected static array $aHttpParts;

	protected static string $sBaseDirPath;
	protected static string $sAppTenantAlias;
	protected static string $sPublicHtmlDir;
	protected static array $aPublicAssetsDirs;
	protected static string $sPublicAssetsPath;
	protected static string $sStorageDir;
	protected static string $sPublicAssetsDirImgWeb;
	protected static string $sPublicAssetsDirCssWeb;
	protected static string $sPublicAssetsDirJsWeb;
	protected static string $sPublicAssetsDirMediaWeb;
	protected static string $sPublicAssetsDirDataWeb;
	protected static string $sTempDir;
	protected static array $aInitSystemDirList = [];
	protected static array $aInitSystemPhpList = [];


	/**
	 * @throws AfrException
	 */
	public static function setBaseDirPath(string $sBaseDirPath): void
	{
		static::isCli();
		if (!empty(self::$sBaseDirPath) && $sBaseDirPath !== self::$sBaseDirPath) {
			throw new AfrException("The base dir path already defined!");
		}
		self::$sBaseDirPath = $sBaseDirPath;
	}

	/**
	 * @throws AfrException
	 */
	public static function loadConfig(string $sBasePath = null): void
	{
		static::isCli();
		if ($sBasePath) {
			self::setBaseDirPath($sBasePath);
		}
		include(self::getBaseDirPath() . DIRECTORY_SEPARATOR . 'tenant.env.php');
		static::processConfig();
	}

	/**
	 * @return void
	 * @throws AfrException
	 */
	protected static function processConfig(): void
	{
		$oTenant = self::resolveTenantAlias();


		$sBaseDirPath = self::getBaseDirPath() . DIRECTORY_SEPARATOR;
		$sTenantSubDir = DIRECTORY_SEPARATOR . static::$sAppTenantAlias;

		static::$aInitSystemPhpList['env'] = static::$sTenantEnvFilePath = $sBaseDirPath . static::$sAppTenantAlias . '.' . $oTenant->sEnv . '.env';
		static::$aInitSystemPhpList['modules'] = static::$sTenantModuleConfigFilePath = $sBaseDirPath . 'modules' . $sTenantSubDir . '.modules.php';
		static::$aInitSystemDirList[] = $sBaseDirPath . 'modules';
		static::$aInitSystemPhpList['routes'] = static::$sTenantRoutesFilePath = $sBaseDirPath . 'routes' . $sTenantSubDir . '.routes.php';
		static::$aInitSystemDirList[] = $sBaseDirPath . 'routes';
		static::$aInitSystemPhpList['toConcreteStrategies'] = static::$sToConcreteStrategiesFilePath = $sBaseDirPath . static::$sAppTenantAlias . '.toConcreteStrategies.php';


		static::$sStorageDir = $sBaseDirPath . 'storage';
		static::$sTempDir = $oTenant->sTmpDir;
		static::$aInitSystemDirList[] = static::getStorageDir();
		static::$aInitSystemDirList[] = static::getTempDir();
		static::$aInitSystemDirList[] = static::$sPublicHtmlDir = $oTenant->sHtmlDir;

		if (static::$sPublicAssetsPath = $oTenant->bAssetDirMapAny ? $oTenant->sAssetsDir : '') {
			static::$aInitSystemDirList[] = static::$sPublicAssetsPath;
		}
		static::$aInitSystemDirList[] = static::$aPublicAssetsDirs = $oTenant->aAssetsExtraDirs;
		static::$sPublicAssetsDirCssWeb = $oTenant->aAssetsExtraDirs['css'];
		static::$sPublicAssetsDirJsWeb = $oTenant->aAssetsExtraDirs['js'];
		static::$sPublicAssetsDirImgWeb = $oTenant->aAssetsExtraDirs['img'];
		static::$sPublicAssetsDirMediaWeb = $oTenant->aAssetsExtraDirs['media'];
		static::$sPublicAssetsDirDataWeb = $oTenant->aAssetsExtraDirs['data'];

		static::$sProtocolDomain = reset($oTenant->aProtocolDomain);
		static::$sWebRoot = $oTenant->sRoot;

		static::$aHttpParts = (array)parse_url(static::$sProtocolDomain . $oTenant->sRoot);


	}

	/**
	 * @throws AfrException
	 */
	public static function initFileSystem(): array
	{
		$aErrors = [];

		static::mkdir(static::$aInitSystemDirList, $aErrors);

		if (!file_exists($sPath = static::getTenantModuleConfigFilePath())) {
			file_put_contents($sPath, '<?php return [];');
			$aErrors[] = 'Module config file blank initialized: ' . $sPath;
		}


		if (!file_exists($sPath = static::getTenantRoutesFilePath())) {
			file_put_contents($sPath, '<?php return [];');
			$aErrors[] = 'Routes file blank initialized : ' . $sPath;
		}

		$sPath = static::getTenantToConcreteStrategiesFilePath();
		if ($sPath && !file_exists($sPath)) {
			file_put_contents(
				$sPath,
				AfrToConcreteStrategiesClass::sampleTenantToConcreteStrategiesFileContents()
			);
			$aErrors[] = 'Tenant To Concrete Strategies sample file initialized : ' . $sPath;
		}



		$ds = DIRECTORY_SEPARATOR;
		if (!is_file($f = self::getTempDir() . $ds . '.gitignore')) {
			file_put_contents($f, "*.php\n*CheckTs\n");
		}

		if (!file_exists(static::getTenantEnvFilePath())) {
			file_put_contents(static::getTenantEnvFilePath(), "APP_ENV_ROCKET=🚀\nMULTI1=foo\nMULTI2=\${MULTI1}");
			$aErrors[] = 'Environment file blank initialized: ' . static::getTenantEnvFilePath();
		}

		if (!empty($aErrors)) {
			throw new AfrException("\n" . implode("\n", $aErrors) . "\n\n");
		}

		return $aErrors;
	}

	private static function mkdir(array $aDirs, array &$aErrors): void
	{
		foreach ($aDirs as $sPath) {
			if (is_string($sPath) && !is_dir($sPath)) {
				if (!mkdir($sPath, 0755, true)) {
					$aErrors[] = 'Dir create error: ' . $sPath;
				}
			} elseif (is_array($sPath)) {
				static::mkdir($sPath, $aErrors);
			}
		}
	}


	/**
	 * @return void
	 * @throws AfrException
	 */
	protected static function resolveTenantAlias(): AfrTenant
	{
		if (empty(static::$sAppTenantAlias)) {
			if (empty(static::$aTenantCfgIns)) {
				//(new AfrTenant('dev'))->setEnv('dev')->setDebug(true)->autoSetupAndPushTenantConfig();
				throw new AfrException(
					'At least one tenant is required for processing configuration.' . PHP_EOL .
					'Check ' . self::getBaseDirPath() . DIRECTORY_SEPARATOR . 'tenant.env.php'
				);
			}

			if (!static::IsCli()) {
				$sProtocolDomain = strtolower($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']);
				/**
				 * HTTP:
				 * $_SERVER['SERVER_NAME'] match inside AfrTenant::HOST_LIST
				 */
				/** @var self $oTenant */
				foreach (static::$aTenantCfgIns as $sAppTenantAlias => $oTenant) {
					if (in_array($sProtocolDomain, $oTenant->aProtocolDomain)) {
						static::$sAppTenantAlias = $sAppTenantAlias;
						break;
					}
				}
			} else {
				/**
				 * CLI:
				 * set using argv params; eg: php index.php
				 * -T="tenantName"
				 * --tenant='sub.domain.tld'
				 * -T tenantName
				 * --tenant 'domain.com'
				 * $sTenantEnv = getenv('AFR_TENANT') ?? $_ENV['AFR_TENANT'] ?? null;
				 * !! set using ENV, but this is not recommended for multi tenant in CLI calling
				 */
				$sTenantArg =
					getopt('T:')['T'] ??
					getopt('T::')['T'] ??
					getopt('', ['tenant:'])['tenant'] ??
					getopt('', ['tenant::'])['tenant'] ??
					$_ENV['AFR_TENANT_CLI'] ??
					getenv('AFR_TENANT_CLI');
				if (!empty($sTenantArg) && !empty(static::$aTenantCfgIns[$sTenantArg])) {
					static::$sAppTenantAlias = $sTenantArg;
				} else {
					if (count(static::$aTenantCfgIns) === 1) {
						static::$sAppTenantAlias = (string)key(static::$aTenantCfgIns);
					} else {
						$options = array_merge(array_keys(static::$aTenantCfgIns));
						static::$sAppTenantAlias = AfrCliPromptMenu::promptMenu(
							"Or run php script.php -T='tenantName' --tenant='sub.domain.tld'",
							$options,
							$options[0],
							2,
							"What tenant to choose from this list?\nAvailable Tenants"
						);
					}
				}
			}
		}

		// FALLBACK: If no tenant match found, then we render the first tenant key
		if (empty(static::$sAppTenantAlias) && defined($sTenantDefault = 'AFR_TENANT_DEFAULT')) {
			static::$sAppTenantAlias = constant($sTenantDefault);
			// static::$sAppTenantAlias = (string)key(static::$aTenantCfgIns);
		}

		if (empty(static::$aTenantCfgIns[static::$sAppTenantAlias])) {
			if (!static::isCli()) {
				http_response_code(421);
				throw new AfrException(
					'Tenant host was not matched for: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
				);
			} else {
				throw new AfrException(
					'Tenant miss configured!' . PHP_EOL . ' Use params eg: ' .
					'php index.php -T="tenantName" --tenant="sub.domain.tld"'
				);
			}

		}

		/** @var AfrTenant $oTenant */
		$oTenant = static::$aTenantCfgIns[static::$sAppTenantAlias];
		foreach (['AFR_ENV' => $oTenant->sEnv, 'AFR_DEBUG' => $oTenant->bDebug] as $key => $value) {
			$_ENV[$key] = $value;
			$_SERVER[$key] = $value;
			putenv(sprintf('%s=%s', $key, $value));
			//if (!defined($key)) {	define($key, $value);	}
		}
		return $oTenant;

	}


}