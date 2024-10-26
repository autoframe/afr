<?php
declare(strict_types=1);

namespace Autoframe\Core\FileSystem\Traversing;


interface AfrDirTraversingCollectionInterface extends
    AfrDirTraversingCountChildrenDirsInterface,
    AfrDirTraversingFileListInterface,
    AfrDirTraversingGetAllChildrenDirsInterface
{

}