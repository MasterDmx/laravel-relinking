<?php

namespace MasterDmx\LaravelRelinking;

use MasterDmx\LaravelRelinking\Collections\PageCollection;
use MasterDmx\LaravelRelinking\ContextManager;
use MasterDmx\LaravelRelinking\Entities\Page;
use MasterDmx\LaravelRelinking\VO\Link;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\VariadicValueResolver;

class RelinkingManager
{
    public function __construct(
        private ContextManager $contexts,
        private RelinkingSupport $support,
    ){}

    /**
     * Возвращает коллекцию страниц
     */
    public function getPages(): PageCollection
    {
        $result = [];

        // Список кол-ва входящих ссылок по страница
        $linksCountCollection = RelinkingModel::query()->getAllLinksCountByPages();

        // Список кол-ва исходящих ссылок по страницам
        $incomingLinksCountCollection = RelinkingModel::query()->getAllIncomingLinksCountByPages();

        // Инициализируем страницы
        foreach ($this->contexts->all() as $context) {
            $pages = $context->all();

            foreach ($pages as $data){
                $linksCount = $incomingLinksCount = 0;

                foreach ($incomingLinksCountCollection as $point){
                    if ($point->to_context === $context->getAlias() && $point->to_id === $data->id) {
                        $incomingLinksCount = $point->count;
                    }
                }

                foreach ($linksCountCollection as $point){
                    if ($point->from_context === $context->getAlias() && $point->from_id === $data->id) {
                        $linksCount = $point->count;
                    }
                }

                $result[] = new Page($data->id, $context, Link::fromContextDTO($data), $linksCount, $incomingLinksCount);
            }
        }

        return new PageCollection($result);
    }

    /**
     * Возвращает объект конкретной страницы
     *
     * @param string $alias
     * @param        $id
     *
     * @return Page
     */
    public function getPage(string $alias, $id): Page
    {
        // Список кол-ва входящих ссылок по страница
        $linksCount = RelinkingModel::query()->getLinksCount($alias, $id);
        $context = $this->contexts->get($alias);
        $data = $context->getDataById($id);

        return new Page($data->id, $context, Link::fromContextDTO($data), $linksCount, 0);
    }

    /**
     * Возвращает коллекцию исходящих ссылок по контексту $alias и $id
     *
     * @param string $alias
     * @param        $id
     *
     * @return Collections\OutgiongLinkCollection
     */
    public function getLinks(string $alias, $id)
    {
        return $this->support->getLinksByRelinkingCollection(RelinkingModel::query()->for($alias, $id)->get());
    }

    /**
     * Возвращает коллекцию входящих ссылок по контексту $alias и $id
     *
     * @param string $alias
     * @param        $id
     *
     * @return Collections\OutgiongLinkCollection
     */
    public function getIncomingLinks(string $alias, $id)
    {
        return $this->support->getIncomingLinksByRelinkingCollection(RelinkingModel::query()->whereIncomingPageIs($alias, $id)->get());
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
    public function generate(string $alias, $id, string $seachText = null): void
    {
        $incomingContext = $this->contexts->get($alias);

        if (!isset($seachText)) {
            $seachText = $incomingContext->getSearchText($id);
        }

        // Кол-во уже имеющихся исходящих ссылок
        $outgoingLinksCount = RelinkingModel::query()->for($alias, $id)->count();

        // Проверяем, не заполнены ли связи
        if ($outgoingLinksCount < $incomingContext->linksLimit()) {
            $availableOutgoingLinks = RelinkingModel::query()->getFor($alias, $id);
            $selected               = new SelectedRelinkingCollection();

            // Проходимся по контекстам
            foreach ($this->contexts->all() as $context) {
                $except = collect();

                // Исключаем из подбора текущий ID сущности
                if ($context->getAlias() === $incomingContext->getAlias()){
                    $except->add($id);
                }

                // Исключаем из подбора ссылки, у которых заполнены входящие ссылки
                if ($overSaturatedLinksIds = RelinkingModel::query()->getIdsWhereCountIncomingLinksInContextMoreThen($context->getAlias(), $context->incomingLinksLimit())) {
                    $except = $except->merge(collect($overSaturatedLinksIds));
                }

                if ($availableOutgoingLinks->isNotEmpty()){
                    $ids = $availableOutgoingLinks->whereOutgoingContext($context)->getOutgoingIds();

                    if ($ids->isNotEmpty()) {
                        $except = $except->merge($ids);
                    }
                }

                // Дополняем общую коллекцию подобранных ссылок
                $selected = $selected->merge(
                    $context->select($seachText, $except->toArray())
                );
            }

            if ($selected->count() > 0) {
                // Сортируем результат по релевантности и оставляем только нужное кол-во
                $selected = $selected->sortByRelevance()->take($context->linksLimit() - $availableOutgoingLinks->count());

                // Регистрируем связи
                RelinkingModel::registerByList($incomingContext, $id, $selected);
            }
        }
    }

    /**
     * Генерирует связи для всех сущностей, у которых есть место
     */
    public function generateAll()
    {
        // Загружаем все контексты из реестра
        $contexts = $this->contexts->all();

        // Запускаем цикл по контекстам
        foreach ($contexts as $context) {
            $context->getAll()->each(function ($item) use ($context) {
                $this->generate($context->getAlias(), $item->id);
            });
        }
    }

    /**
     * Возвращает массив всех контекстов
     *
     * @return Context[]
     */
    public function getContexts()
    {
        return $this->contexts->all();
    }

    /**
     * Сбрасывает все связи
     */
    public function reset(): void
    {
        RelinkingModel::query()->truncate();
    }
}
