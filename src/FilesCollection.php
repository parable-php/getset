<?php declare(strict_types=1);

namespace Parable\GetSet;

use Parable\GetSet\Resource\GlobalResourceInterface;

class FilesCollection extends BaseCollection implements GlobalResourceInterface
{
    public function getResource(): string
    {
        return '_FILES';
    }
}
