<?php

namespace MasterDmx\LaravelRelinking;

use MasterDmx\LaravelRelinking\Contexts\ContextRegistry;
use MasterDmx\LaravelRelinking\Ligaments\LigamentSupport;
use MasterDmx\LaravelRelinking\Links\Link;
use MasterDmx\LaravelRelinking\Links\LinkCollection;
use MasterDmx\LaravelRelinking\Links\LinkSupport;

class RelinkingManager
{
    private ContextRegistry $contexts;
    private LinkSupport $links;
    private LigamentSupport $ligaments;

    public function __construct(ContextRegistry $contexts, LinkSupport $links, LigamentSupport $ligaments)
    {
        $this->contexts = $contexts;
        $this->links = $links;
        $this->ligaments = $ligaments;
    }

    /**
     * Добавляет новую ссылку
     *
     * @param string $context
     * @param        $id
     * @param string $text
     */
    public function add(string $context, $id, string $text): void
    {
        $this->update($context, $id, $text);
    }

    /**
     * Обновляет недостающие связи
     *
     * @param string $context
     * @param        $id
     * @param string $text
     */
    public function update(string $context, $id, string $text): void
    {
        $this->ligaments->generate($this->links->getOrAdd($context, $id), $text);
    }

    /**
     * Удаляет ссылку и все связи
     *
     * @param string $context
     * @param        $id
     *
     * @throws \Exception
     */
    public function remove(string $context, $id): void
    {
        $this->links->remove($context, $id);
    }

    public function all(): LinkCollection
    {
        return $this->links->getAll();
    }

    /**
     * @param string $alias
     * @param        $itemId
     *
     * @return LinkCollection
     */
    public function getLinksFor(string $alias, $itemId): LinkCollection
    {
        $all = Link::query()->whereLigamentsContextAndId($alias, $itemId)->withCountLigaments()->get();

        foreach ($all->groupByContext() as $context => $links) {
            $links->saturate(
                $links->first()->getContext()->saturateCollection($links->getIds()->toArray())
            );
        }

        return $all;
    }
}
