<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Support\Collection;

/**
 * Class SelectedItemCollection
 *
 * @property OutgoingRelinkingItem[] $items
 *
 * @method  OutgoingRelinkingItem[] all()
 *
 * @package MasterDmx\LaravelRelinking
 */
class SelectedRelinkingCollection extends Collection
{
    /**
     * Сортировать ссылки по релевантности
     *
     * @return SelectedRelinkingCollection
     */
    public function sortByRelevance(): SelectedRelinkingCollection
    {
        return $this->sortBy(function ($item) {
            return -$item->relevance;
        });
    }
}
