<?php

namespace MasterDmx\LaravelRelinking;

use MasterDmx\LaravelRelinking\Contracts\Linkable;

class LinkableRegistry
{
    /**
     * @var Linkable[]
     */
    private static array $data;

    public static function fromArray(array $list)
    {
        foreach ($list as $class) {
            static::add($class);
        }
    }

    /**
     * @return Linkable[]
     */
    public static function all(): array
    {
        return static::$data;
    }

    public static function get(string $class): Linkable
    {
        if (static::has($class)){
            return clone static::$data[$class];
        }

        throw new \RuntimeException('Linkable class ' . $class . ' not registered');
    }

    public static function add(string $class): Linkable
    {
        return static::$data[$class] = app($class);
    }

    public static function has(string $class): bool
    {
        return isset(static::$data[$class]);
    }
}
