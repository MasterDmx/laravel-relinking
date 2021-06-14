<?php

namespace MasterDmx\LaravelRelinking;

use MasterDmx\LaravelRelinking\ContextRegistry;

class RelinkingManager
{
    private ContextRegistry $contexts;

    public function __construct(ContextRegistry $contexts)
    {
        $this->contexts = $contexts;
    }

    /**
     * Получает связи под контекст и ID (основной метод)
     *
     * @param string $alias
     * @param        $id
     *
     * @return RelinkingCollection
     */
    public function get(string $alias, $id): RelinkingCollection
    {
        $links = Relinking::query()->select('id', 'context_alias', 'context_id', 'relevance')->getFor($alias, $id);

        $this->saturateCollectionLinksData($links);

        return $links;
    }

    /**
     * Генерирует связи, если в контексте есть место
     *
     * @param string $alias
     * @param        $id
     * @param string $text
     *
     * @return void
     */
    public function generate(string $alias, $id, string $text): void
    {
        // Объект контекста
        $context = $this->contexts->get($alias);

        // Проверяем, не заполнены ли связи
        if (Relinking::query()->for($alias, $id)->count() < $context->getLimit()) {
            // Получаем имеющиеся связи для исключения их из выборки
            $availableLinks = Relinking::query()->getFor($alias, $id);

            // Коллекция потенциальных ссылок
            $potentialLinks = new PotentialLinkCollection();

            // Выполняем подбор
            foreach ($this->contexts->all() as $selectableContext) {
                $except = collect();

                if ($selectableContext->getAlias() === $context->getAlias()){
                    $except->add($id);
                }

                $except = $except->merge($availableLinks->whereContextAlias('article')->getContextIds());

                // Через контекст формируем потенциальные ссылки, присваеваем им текущий выбираемый контекст и вносим в общую коллекцию
                $potentialLinks = $potentialLinks->merge($selectableContext->search($text, $except->toArray())->setContext($selectableContext));
            }

            if ($potentialLinks->count() > 0) {
                // Сортируем результат по релевантности и оставляем только нужное кол-во
                $potentialLinks = $potentialLinks->sortByRelevance()->take($context->getLimit() - $availableLinks->count());

                // Регистрируем связи
                Relinking::registerByList($alias, $id, $potentialLinks);
            }
        }
    }

    /**
     * Удаляет перелинковку конкретного элемента контекста и генерирует связи заного
     *
     * @param string $alias
     * @param        $id
     * @param string $text
     */
    public function refresh(string $alias, $id, string $text): void
    {
        $this->remove($alias, $id);
        $this->generate($alias, $id, $text);
    }

    /**
     * Удаляет перелинковку конкретного элемента контекста
     */
    public function remove(string $alias, $id): bool
    {
        return Relinking::query()->for($alias, $id)->delete();
    }

    /**
     * Насыщает элементы перелинковки данными ссылок
     *
     * @param RelinkingCollection $links
     *
     * @return void
     */
    private function saturateCollectionLinksData(RelinkingCollection $links): void
    {
        foreach ($links->groupByContext() as $contextAlias => $links) {
            $instances = $this->contexts->get($contextAlias)->getLinksData($links->getContextIds()->toArray());
            $links->setLinksData($instances);
        }
    }
}
