<?php

namespace Autoframe\Core\Router;

use Autoframe\Core\CliTools\AfrCliDetect;
use Autoframe\Core\CliTools\AfrCliTextColors;
use Autoframe\Core\Env\AfrEnv;
use Autoframe\Core\Router\Contracts\AfrRouterCliInterface;
use Autoframe\Core\Tenant\AfrTenant;

class CliCache implements AfrRouterCliInterface
{
	public function __invoke(): void
	{
		if (AfrCliDetect::isCli()) {
			$aMethods = array_diff(get_class_methods($this), ['__invoke', 'getCollectedResultsFromRoutes']);

			$sCliMethodToCall = getopt('', ['afrCli:'])['afrCli'] ?? getopt('', ['afrCli::'])['afrCli'] ?? null;
			if (in_array($sCliMethodToCall, $aMethods)) {
				$this->$sCliMethodToCall();
				//TODO: de adaugat else if @class@method
			} else {
				$aOptions = [];
				foreach ($aMethods as $sMethod) {
					$aOptions[$sMethod] = function () use ($sMethod) {
						return $this->$sMethod();
					};
				}
				while (true) {
					if (AfrCliRouterHelper::cliQA($aOptions) === null) {
						break;
					}
				}
			}


		}
	}


	protected function clearCache(): bool
	{
		while (true) {
			$sEnvCacheFile = AfrEnv::getInstance()->getCacheFileName();
			$sTxt = ' Cache for AfrEnv->readEnv: ' . basename($sEnvCacheFile);
			if (is_file($sEnvCacheFile)) {
				$k = 'Clear' . $sTxt;
				$v = function () use ($sEnvCacheFile) {
					return unlink($sEnvCacheFile);
				};
			} else {
				$k = AfrCliRouterHelper::cliItalicGrey('Not found' . $sTxt);
				$v = false;
			}
			$aOptions = [$k => $v,];
			if (AfrCliRouterHelper::cliQA($aOptions) === null) {
				break;
			}
		}
		return true;
	}

	protected function initTenantFileSystem(): bool
	{
		while (true) {
			$aOptions = [
				'Init Tenant File System Directories' => function () {
					$r = AfrTenant::initFileSystem();
					if (count($r) > 0) {
						AfrCliTextColors::getInstance()
							->textAppend("\n\t")
							->colorRed("Errors:\n".implode("\n",$r)."\n")
							->textPrint();
						return false;
					}
					return true;
				},];
			if (AfrCliRouterHelper::cliQA($aOptions) === null) {
				break;
			}
		}
		return true;
	}


	public function getCollectedResultsFromRoutes(): ?array
	{
		// TODO: Implement getCollectedResultsFromRoutes() method.
		return null;
	}
}