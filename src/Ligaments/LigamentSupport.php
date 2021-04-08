<?php

namespace MasterDmx\LaravelRelinking\Ligaments;

use MasterDmx\LaravelRelinking\Contexts\Context;
use MasterDmx\LaravelRelinking\Contexts\ContextRegistry;
use MasterDmx\LaravelRelinking\Links\Link;
use MasterDmx\LaravelRelinking\Links\LinkCollection;
use MasterDmx\LaravelRelinking\Relevance\RelevanceCollection;

class LigamentSupport
{
    private ContextRegistry $contexts;

    public function __construct(ContextRegistry $contexts)
    {
        $this->contexts = $contexts;
    }

    public function generate(Link $link, string $text)
    {
        if ($link->hasFreeLigaments()) {
            $link->setLigaments($this->select($link, $text));
        }
    }

    /**
     * Подобрать потенциальные связи для ссылки
     *
     * @param Link   $link
     * @param string $text
     *
     * @return RelevanceCollection
     */
    public function select(Link $link, string $text): RelevanceCollection
    {
        $all          = new RelevanceCollection();
        $inStock      = $link->getLigaments()->pluck('item_id')->toArray();
        $groupedLinks = Link::query()->getAllCountLigaments()->groupByContext();

        // Получаем свободные ссылки всех контекстов
        foreach ($this->contexts->all() as $context) {
            if (!$groupedLinks->has($context->getAlias())) {
                continue;
            }

            /** @var LinkCollection $links */
            $links = $groupedLinks->get($context->getAlias());

            // Формируем массив исключений
            $excepted = $links->hasFreeLigamentsByCount()->getIds()->add($link->getId());

            if (!empty($inStock)) {
                $excepted = $excepted->merge($inStock);
            }

            // Формируем релевантные ссылки
            $all = $all->merge(
                $context->search($text, $excepted->toArray())->setContext($context)
            );
        }

        return $all->sortByRelevance()->take($link->context->getLimit());
    }
}
