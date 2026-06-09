<?php

namespace App\Core;

use ReflectionClass;

class Container
{
    private array $instances = [];
    private array $factories = [];

    public function set(string $id, object $instance): void
    {
        $this->instances[$id] = $instance;
    }

    public function factory(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
    }

    public function get(string $id): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }
        if (!isset($this->factories[$id])) {
            throw new \RuntimeException("No definition for: $id");
        }
        return $this->instances[$id] = ($this->factories[$id])($this);
    }

    public function has(string $id): bool
    {
        return isset($this->instances[$id]) || isset($this->factories[$id]);
    }

    public function resolve(string $class): object
    {
        if ($this->has($class)) {
            return $this->get($class);
        }

        $ref = new ReflectionClass($class);
        $ctor = $ref->getConstructor();

        if (!$ctor) {
            return $ref->newInstance();
        }

        $params = [];
        foreach ($ctor->getParameters() as $param) {
            $type = $param->getType();

            if (!$type || $type->isBuiltin()) {
                if ($param->isDefaultValueAvailable()) {
                    $params[] = $param->getDefaultValue();
                    continue;
                }
                throw new \RuntimeException("Cannot resolve parameter \${$param->getName()} for $class");
            }

            $typeName = $type->getName();
            if ($this->has($typeName)) {
                $params[] = $this->get($typeName);
            } elseif ($param->isDefaultValueAvailable()) {
                $params[] = $param->getDefaultValue();
            } else {
                throw new \RuntimeException("Cannot resolve \${$param->getName()}: $typeName not found in container");
            }
        }

        return $ref->newInstanceArgs($params);
    }
}
