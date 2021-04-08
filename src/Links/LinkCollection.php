<?php

namespace MasterDmx\LaravelRelinking\Links;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

/**
 * Class LinkCollection
 *
 * @method Link[] all()
 *
 * @package MasterDmx\LaravelRelinking\Links
 */
class LinkCollection extends EloquentCollection
{
    /**
     * Разбить коллекцию на разные коллекции, содержащие только ссылки определенного контекста
     *
     * @return LinkCollection
     */
    public function groupByContext()
    {
        return $this->groupBy(function ($item, $key) {
            return $item->context->getAlias();
        });
    }

    /**
     * Имеет свободные связи
     *
     * @return LinkCollection
     */
    public function hasFreeLigamentsByCount(): LinkCollection
    {
        return $this->filter(fn ($item) => $item->hasFreeLigamentsByCount());
    }

    /**
     * Получить ID
     *
     * @return Collection
     */
    public function getIds(): Collection
    {
        return $this->pluck('item_id');
    }

    /**
     * Получить ссылку по Item ID
     *
     * @param string $id
     *
     * @return Link|null
     */
    public function findByItemId(string $id): ?Link
    {
        foreach ($this->all() as $item) {
            if ($item->item_id == $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param LinkSaturateDTO[] $list
     */
    public function saturate(array $list): void
    {
        foreach ($list as $item) {
            if ($link = $this->findByItemId($item->getId())) {
                $link->saturate($item);
            }
        }
    }
}
