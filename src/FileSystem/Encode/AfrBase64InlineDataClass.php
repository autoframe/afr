<?php
declare(strict_types=1);

namespace Autoframe\Core\FileSystem\Encode;

use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonAbstractClass;

class AfrBase64InlineDataClass extends AfrSingletonAbstractClass implements AfrBase64InlineDataInterface
{
    use AfrBase64InlineDataTrait;

}