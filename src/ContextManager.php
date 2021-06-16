<?php

namespace MasterDmx\LaravelRelinking;

use MasterDmx\LaravelRelinking\Exceptions\UndefinedContextException;
use function app;

class ContextManager
{
    /**
     * @var array|string[]
     */
    private array $contexts = [];

    /**
     * Добавить контекст
     *
     * @param string $class
     *
     * @return $this
     */
    public function add(string $class): ContextManager
    {
        // Привязываем обозначение к конкретному классу
        $this->contexts[$class::alias()] = $class;

        // Указываем контейнеру, что контекст должен быть синглтоном
        app()->singleton($class);

        return $this;
    }

    /**
     * Добавить контексты из массива
     *
     * @param array $data
     *
     * @return $this
     */
    public function addFromArray(array $data): ContextManager
    {
        foreach ($data as $class) {
            $this->add($class);
        }

        return $this;
    }

    /**
     * Проверить наличие
     *
     * @param string $alias
     *
     * @return bool
     */
    public function has(string $alias): bool
    {
        return isset($this->contexts[$alias]);
    }

    /**
     * Получить инстанс
     *
     * @param $alias
     *
     * @return Context
     */
    public function get($alias): Context
    {
        if (!$this->has($alias)) {
            throw new UndefinedContextException('Undefined context "' . $alias . '"');
        }

        return app($this->contexts[$alias]);
    }

    public function createPersonalInstance(string $alias, $id): Context
    {
        return $this->get($alias)->createItem($id);
    }

    /**
     * Получить класс по названию вариации
     *
     * @param string $alias
     *
     * @return Context
     */
    public function getClass(string $alias): Context
    {
        return $this->contexts[$alias];
    }

    /**
     * @return Context[]
     */
    public function all(): array
    {
        $list = [];

        foreach ($this->contexts as $alias => $class) {
            $list[] = $this->get($alias);
        }

        return $list;
    }
}
