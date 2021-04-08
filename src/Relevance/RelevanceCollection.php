<?php

namespace MasterDmx\LaravelRelinking\Relevance;

use Illuminate\Support\Collection;
use MasterDmx\LaravelRelinking\Contexts\Context;

/**
 * Class LinkCollection
 *
 * @property Relevance[] $items
 *
 * @method Relevance[] all()
 *
 * @package MasterDmx\LaravelRelinking\Links
 */
class RelevanceCollection extends Collection
{
    /**
     * Сортировать ссылки по релевантности
     *
     * @return RelevanceCollection
     */
    public function sortByRelevance(): RelevanceCollection
    {
        return $this->sortBy(function ($item) {
            return -$item->getRelevance();
        });
    }

    public function setContext(Context $context): RelevanceCollection
    {
        return $this->map(fn($item) => $item->setContext($context));
    }
}
