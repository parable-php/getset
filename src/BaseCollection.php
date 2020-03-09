<?php declare(strict_types=1);

namespace Parable\GetSet;

use Parable\GetSet\Resource\GlobalResourceInterface;
use Parable\GetSet\Resource\LocalResourceInterface;

abstract class BaseCollection
{
    /**
     * @var array
     */
    protected $localValues = [];

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

        foreach ($keys as $key) {
            if (!isset($resource[$key])) {
                return $default;
            }

            $resource = &$resource[$key];
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

        foreach ($keys as $key) {
            if (!isset($resource[$key]) || !is_array($resource[$key])) {
                $resource[$key] = [];
            }

            $resource = &$resource[$key];
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

        foreach ($keys as $index => $key) {
            if (!isset($resource[$key])) {
                throw new Exception(sprintf(
                    "Cannot remove non-existing value by key '%s'",
                    $key
                ));
            }

            if ($index < (count($keys) - 1)) {
                $resource = &$resource[$key];
            }
        }

        unset($resource[$key]);

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

        foreach ($keys as $key) {
            if (!isset($resource[$key])) {
                return false;
            }

            $resource = &$resource[$key];
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
