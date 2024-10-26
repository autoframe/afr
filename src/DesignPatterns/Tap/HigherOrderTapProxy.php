<?php

namespace Autoframe\Core\DesignPatterns\Tap;

class HigherOrderTapProxy
{
    /**
     * The Target being tapped.
     *
     * @var mixed
     */
    public $higherOrderTapProxyTarget;

    /**
     * Create a new tap proxy instance.
     *
     * @param  mixed  $higherOrderTapProxyTarget
     * @return void
     */
    public function __construct($higherOrderTapProxyTarget)
    {
        $this->higherOrderTapProxyTarget = $higherOrderTapProxyTarget;
    }

    /**
     * Dynamically pass method calls to the higherOrderTapProxyTarget.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        $this->higherOrderTapProxyTarget->{$method}(...$parameters);

        return $this->higherOrderTapProxyTarget;
    }


}
