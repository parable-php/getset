<?php declare(strict_types=1);

namespace Parable\GetSet;

use Parable\GetSet\Resource\GlobalResourceInterface;
use Parable\GetSet\Resource\LocalResourceInterface;

abstract class BaseCollection
{
    protected array $localValues = [];

    public function getAll(): array
    {
        if ($this instanceof LocalResourceInterface) {
            return $this->localValues;
        }

        if ($this instanceof GlobalResourceInterface) {
            if (!isset($GLOBALS[$this->getResource()])) {
                $GLOBALS[$this->getResource()] = [];
            }

            return $GLOBALS[$this->getResource()];
        }

        throw new Exception('No resource interface implemented.');
    }

    public function getAllAndClear(): array
    {
        $data = $this->getAll();

        $this->clear();

        return $data;
    }

    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);

        $resource = $this->getAll();

        foreach ($keys as $keyPart) {
            if (!isset($resource[$keyPart])) {
                return $default;
            }

            $resource = &$resource[$keyPart];
        }

        return $resource;
    }

    public function getMultiple(string ...$keys): array
    {
        $values = [];

        foreach ($keys as $key) {
            $values[] = $this->get($key);
        }

        return $values;
    }

    public function getAndRemove(string $key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        $data = $this->get($key);

        $this->remove($key);

        return $data;
    }

    public function set(string $key, $value): void
    {
        $keys = explode('.', $key);

        $data = $this->getAll();

        $resource = &$data;

        foreach ($keys as $keyPart) {
            if (!isset($resource[$keyPart]) || !is_array($resource[$keyPart])) {
                $resource[$keyPart] = [];
            }

            $resource = &$resource[$keyPart];
        }

        $resource = $value;

        $this->setAll($data);
    }

    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function setAll(array $values): void
    {
        if ($this instanceof LocalResourceInterface) {
            $this->localValues = $values;
            return;
        }

        if ($this instanceof GlobalResourceInterface) {
            $GLOBALS[$this->getResource()] = $values;
            return;
        }

        throw new Exception('No resource interface implemented.');
    }

    public function remove(string $key): void
    {
        $keys = explode('.', $key);

        $data = $this->getAll();

        $resource = &$data;

        foreach ($keys as $index => $keyPart) {
            if (!isset($resource[$keyPart])) {
                throw new Exception(sprintf(
                    "Cannot remove non-existing value by key '%s'",
                    $key
                ));
            }

            if ($index < (count($keys) - 1)) {
                $resource = &$resource[$keyPart];
            }
        }

        unset($resource[$keyPart]);

        $this->setAll($data);
    }

    public function clear(): void
    {
        $this->setAll([]);
    }

    public function has(string $key): bool
    {
        $keys = explode('.', $key);

        $resource = $this->getAll();

        foreach ($keys as $keyPart) {
            if (!isset($resource[$keyPart])) {
                return false;
            }

            $resource = &$resource[$keyPart];
        }

        return true;
    }

    public function count(string $key = null): int
    {
        if ($key === null) {
            $data = $this->getAll();
        } else {
            $data = $this->get($key, []);
        }

        return count($data);
    }
}
