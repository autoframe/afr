<?php
declare(strict_types=1);

namespace Autoframe\Core\FileSystem;

use Autoframe\Core\FileSystem\DirPath\AfrDirPathTrait;
use Autoframe\Core\FileSystem\DirPath\AfrDirPathInterface;
use Autoframe\Core\FileSystem\Encode\AfrBase64InlineDataTrait;
use Autoframe\Core\FileSystem\Encode\AfrBase64InlineDataInterface;
use Autoframe\Core\FileSystem\OverWrite\AfrOverWriteInterface;
use Autoframe\Core\FileSystem\OverWrite\AfrOverWriteTrait;
use Autoframe\Core\FileSystem\SplitMerge\AfrSplitMergeInterface;
use Autoframe\Core\FileSystem\SplitMerge\AfrSplitMergeTrait;
use Autoframe\Core\FileSystem\Traversing\AfrDirTraversingCollectionTrait;
use Autoframe\Core\FileSystem\Traversing\AfrDirTraversingCollectionInterface;
use Autoframe\Core\FileSystem\Versioning\AfrDirMaxFileMtimeInterface;
use Autoframe\Core\FileSystem\Versioning\AfrDirMaxFileMtimeTrait;
use Autoframe\Core\FileSystem\Versioning\AfrFileVersioningMtimeHashInterface;
use Autoframe\Core\FileSystem\Versioning\AfrFileVersioningMtimeHashTrait;
use Autoframe\Core\DesignPatterns\Singleton\AfrSingletonAbstractClass;

class AfrFileSystemCollectionClass extends AfrSingletonAbstractClass implements
    AfrDirPathInterface,
    AfrBase64InlineDataInterface,
    AfrDirTraversingCollectionInterface,
    AfrDirMaxFileMtimeInterface,
    AfrFileVersioningMtimeHashInterface,
    AfrOverWriteInterface,
    AfrSplitMergeInterface
{
    use AfrDirPathTrait;
    use AfrBase64InlineDataTrait;
    use AfrDirTraversingCollectionTrait;
    use AfrDirMaxFileMtimeTrait;
    use AfrFileVersioningMtimeHashTrait;
    use AfrOverWriteTrait;
    use AfrSplitMergeTrait;
}
