<?php declare(strict_types=1);

namespace Parable\GetSet;

use Parable\GetSet\Resource\GlobalResourceInterface;

class ServerCollection extends BaseCollection implements GlobalResourceInterface
{
    public function getResource(): string
    {
        return '_SERVER';
    }
}
