<?php
declare(strict_types=1);

namespace Autoframe\Core\Container;

use Autoframe\Core\ClassDependency\AfrClassDependencyException;
use Autoframe\Core\Env\Exception\AfrEnvException;
use Autoframe\Core\InterfaceToConcrete\AfrInterfaceToConcreteInterface;
use Autoframe\Core\InterfaceToConcrete\AfrInterfaceToConcreteClass;
use Autoframe\Core\InterfaceToConcrete\Exception\AfrInterfaceToConcreteException;
use Autoframe\Core\Env\AfrEnv;

use Closure;
use Autoframe\Core\Container\LaraPort\Container;
use Autoframe\Core\Container\Exception\BindingResolutionException;
use Autoframe\Core\Container\Exception\CircularDependencyException;

class AfrContainerOverdrive extends Container
{

	protected ?AfrInterfaceToConcreteInterface $AfrInterfaceToConcreteInterface = null;

	/**
	 * First it will try to auto-map the interface or abstract class to a
	 * concrete implementation according to some rules. If it fails to map,
	 * then throws an exception that the concrete is not instantiable.
	 *
	 * @param string $concrete
	 * @return Closure|mixed|void|null
	 * @throws AfrClassDependencyException
	 * @throws AfrInterfaceToConcreteException
	 * @throws AfrEnvException
	 * @throws BindingResolutionException|CircularDependencyException|\ReflectionException
	 */
	protected function notInstantiable(string $concrete)
	{
		/*$oAfrToConcreteStrategiesClass = AfrToConcreteStrategiesClass::getLatestInstance();
		list($sFlag, $implementingClass) = explode('|', $oAfrToConcreteStrategiesClass->resolveInterfaceToConcrete(
			$concrete, //not concrete, but the interface call this concrete :P
			$this->getAfrInterfaceToConcrete()
		));*/

		list($sFlag, $implementingClass) = explode(
			'|',
			$this->getAfrInterfaceToConcrete()->resolve(
				$concrete, //not concrete, but the interface call this concrete :P
				$bUseCache = true,
				$sTemporaryContextOverwrite = null, //$this->getContext()
				$sTemporaryPriorityRuleOverwrite = null
			)
		);
		if ($sFlag === '1' || $sFlag === '2') {
			return $sFlag === '2' ?
				$implementingClass::getInstance() :
				//$this->build($implementingClass); //todo build vs make
				$this->make($implementingClass);
			//TODO AFR CONFIG FACTORY
		}


		//resolve failed, so run original code and throw error
		parent::notInstantiable($concrete);
	}

	/**
	 * @param AfrInterfaceToConcreteInterface $AfrConfigWiredPathsClass
	 * @return self
	 */
	public function setConfigWiredPathsClass(AfrInterfaceToConcreteInterface $AfrConfigWiredPathsClass): self
	{
		$this->AfrInterfaceToConcreteInterface = $AfrConfigWiredPathsClass;
		return $this;
	}


	/**
	 * @return AfrInterfaceToConcreteInterface
	 * @throws AfrInterfaceToConcreteException|AfrEnvException
	 */
	protected function getAfrInterfaceToConcrete(): AfrInterfaceToConcreteInterface
	{
		if (empty($this->AfrInterfaceToConcreteInterface)) {
			if (!empty(AfrInterfaceToConcreteClass::$oLatestInstance)) {
				$this->AfrInterfaceToConcreteInterface = AfrInterfaceToConcreteClass::$oLatestInstance;
			} else {
				$this->AfrInterfaceToConcreteInterface = new AfrInterfaceToConcreteClass(
					AfrEnv::getInstance()->getEnv('AFR_ENV', 'PRODUCTION')
				);
			}
		}
		return $this->AfrInterfaceToConcreteInterface;
	}


	// TODO set prioritati
	// profile prioritati
	// set rules array

	protected ?string $sContext = null;


	public function setContext(string $sContext = null): self
	{
		$this->sContext = $sContext;
		//TODO de pus in context pt resolving strategies
		return $this;
	}

	public function getContext(): ?string
	{
		return $this->sContext;
	}

}