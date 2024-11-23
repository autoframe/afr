<?php

namespace Autoframe\Core\Container;

use Autoframe\Core\Container\Exception\AfrContainerException;
use ReflectionException;
use ArrayAccess;


interface AfrContainerInterface extends ArrayAccess
{
	/**
	 * @return AfrContainerInterface
	 */
	public static function getInstance(): AfrContainerInterface;

	/**
	 * @param string $id
	 * @return mixed
	 * @throws AfrContainerException
	 * @throws ReflectionException
	 */
	public function get(string $id);

	/**
	 * @param string $abstract
	 * @param array $parameters
	 * @return mixed
	 * @throws AfrContainerException
	 * @throws ReflectionException
	 */
	public function make(string $abstract, array $parameters = []);

	/**
	 * @param string $abstract
	 * @return bool
	 */
	public function has(string $abstract): bool;

	/**
	 * @param string $abstract
	 * @param callable|string $concrete
	 * @param bool $shared
	 * @return void
	 * @throws AfrContainerException
	 */
	public function bind(string $abstract, $concrete, bool $shared = false): void;

	/**
	 * @param string $abstract
	 * @param mixed $instance
	 * @return mixed
	 */
	public function registerInstance(string $abstract, $instance);
}