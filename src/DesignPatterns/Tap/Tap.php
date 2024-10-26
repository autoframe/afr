<?php

namespace Autoframe\Core\DesignPatterns\Tap;

class Tap
{
    /**
     * Call the given Closure with the given value then return the value.
     *
     * @param  mixed  $value
     * @param callable|null $callback
     * @return mixed
     */
    public static function tap($value, ?callable $callback = null)
    {
        return is_null($callback) ? new HigherOrderTapProxy($value) : $callback($value);
    }
}