<?php

namespace MasterDmx\LaravelRelinking\Contexts;

use Illuminate\Container\Container;

class ContextRegistry
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
    public function add(string $class): ContextRegistry
    {
        $alias = $class::alias();
        $this->getContainer()->singleton($class);
        $this->contexts[$alias] = $class;

        return $this;
    }

    /**
     * Добавить контексты из массива
     *
     * @param array $data
     *
     * @return $this
     */
    public function addFromArray(array $data): ContextRegistry
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
     * Получить название вариации по классу
     *
     * @param $class
     *
     * @return string
     */
    public function getName($class): string
    {
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

    /**
     * Получить контейнер
     *
     * @return Container
     */
    private function getContainer(): Container
    {
        return Container::getInstance();
    }
}
