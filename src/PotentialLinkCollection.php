<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Support\Collection;
use MasterDmx\LaravelRelinking\Context;

/**
 * Class LinkCollection
 *
 * @property PotentialLink[] $items
 *
 * @method PotentialLink[] all()
 *
 * @package MasterDmx\LaravelRelinking\Links
 */
class PotentialLinkCollection extends Collection
{
    /**
     * Сортировать ссылки по релевантности
     *
     * @return PotentialLinkCollection
     */
    public function sortByRelevance(): PotentialLinkCollection
    {
        return $this->sortBy(function ($item) {
            return -$item->getRelevance();
        });
    }

    /**
     * Задает контекст всем ссылкам в коллекции
     *
     * @param Context $context
     *
     * @return PotentialLinkCollection
     */
    public function setContext(Context $context): PotentialLinkCollection
    {
        return $this->map(fn($item) => $item->setContext($context));
    }

    /**
     * Возвращает коллекцию ID потенциальных ссылок
     *
     * @return $this
     */
    public function getIds()
    {
        $ids = [];

        foreach ($this->all() as $item){
            $ids[] = $item->getId();
        }

        return new static($ids);
    }
}
