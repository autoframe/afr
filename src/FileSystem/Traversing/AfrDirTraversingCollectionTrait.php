<?php
declare(strict_types=1);

namespace Autoframe\Core\FileSystem\Traversing;

trait AfrDirTraversingCollectionTrait
{
    use AfrDirTraversingDependency;
    use AfrDirTraversingCountChildrenDirsTrait;
    use AfrDirTraversingFileListTrait;
    use AfrDirTraversingGetAllChildrenDirsTrait;
}
