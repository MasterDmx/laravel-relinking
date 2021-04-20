<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

/**
 * Class LinkCollection
 * @method Relinking[] all()
 *
 * @package MasterDmx\LaravelRelinking\Links
 */
class RelinkingCollection extends EloquentCollection
{
    /**
     * Разбить коллекцию на разные коллекции, содержащие только ссылки определенного контекста
     *
     * @return RelinkingCollection
     */
    public function groupByContext(): RelinkingCollection
    {
        return $this->groupBy(function ($item, $key) {
            return $item->context->getAlias();
        });
    }

    /**
     * Фильтрует ссылки по обозначению контекста
     *
     * @param string $alias
     *
     * @return RelinkingCollection
     */
    public function whereContextAlias(string $alias): RelinkingCollection
    {
        return $this->filter(fn ($link) => $link->context_alias === $alias);
    }

    /**
     * Получить ID
     *
     * @return Collection
     */
    public function getContextIds(): Collection
    {
        return $this->pluck('context_id');
    }

    /**
     * @param Collection $collection
     *
     * @return RelinkingCollection
     */
    public function setLinksData(Collection $collection): RelinkingCollection
    {
        foreach ($this->all() as $link){
            $data = $collection->filter(fn ($item) => $item->getId() === $link->context_id)->first();

            if (isset($data)) {
                $link->setData($data);
            }
        }

        return $this;
    }
}
