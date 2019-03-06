<?php declare(strict_types=1);

namespace Parable\GetSet\Resource;

interface GlobalResourceInterface extends ResourceInterface
{
    public function getResource(): string;
}
