<?php

namespace MasterDmx\LaravelRelinking\Links;

use MasterDmx\LaravelRelinking\Contexts\Context;
use MasterDmx\LaravelRelinking\Contexts\ContextRegistry;

class LinkSupport
{
    private ContextRegistry $contexts;

    public function __construct(ContextRegistry $contexts)
    {
        $this->contexts = $contexts;
    }

    /**
     * Проверяет существование ссылки
     *
     * @param Context|string $context
     * @param string  $id
     *
     * @return bool
     */
    public function has($context, string $id): bool
    {
        $context = $this->getContext($context);

        return Link::query()->checkExists($context->getAlias(), $id);
    }

    /**
     * Получает модель ссылки
     *
     * @param Context|string $context
     * @param string  $id
     *
     * @return Link|null
     */
    public function get($context, string $id): ?Link
    {
        $context = $this->getContext($context);

        return Link::query()->findByContext($context->getAlias(), $id);
    }

    /**
     * Создает модель ссылки
     *
     * @param Context|string $context
     * @param string  $id
     *
     * @return Link
     */
    public function add($context, string $id): ?Link
    {
        $context = $this->getContext($context);

        return Link::add($context->getAlias(), $id);
    }

    /**
     * Получает модель ссылки, или создает ее и возвращает, если ее нет
     *
     * @param Context|string $context
     * @param string  $id
     *
     * @return Link|null
     */
    public function getOrAdd($context, string $id): ?Link
    {
        $context = $this->getContext($context);

        return $this->has($context, $id) ? $this->get($context, $id) : $this->add($context, $id);
    }

    /**
     * Удалить ссылку и все связи
     *
     * @param string $context
     * @param        $id
     *
     * @throws \Exception
     */
    public function remove(string $context, $id): void
    {
        if ($link = Link::query()->findByContext($context, $id)) {
            $link->remove();
        }
    }

    /**
     * Получить все ссылки перелинковки
     *
     * @return LinkCollection
     */
    public function getAll(): LinkCollection
    {
        $all = Link::query()->withCountLigaments()->get();

        foreach ($all->groupByContext() as $context => $links) {
            $links->saturate(
                $links->first()->getContext()->saturateCollection($links->getIds()->toArray())
            );
        }

        return $all;
    }

    protected function getContext($context): Context
    {
        if (is_string($context)) {
            return $this->contexts->get($context);
        }

        return $context;
    }
}
