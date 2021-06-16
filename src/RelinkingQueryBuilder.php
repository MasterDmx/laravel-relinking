<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use MasterDmx\LaravelRelinking\Links\Relinking;
use MasterDmx\LaravelRelinking\RelinkingModelCollection;

/**
 * Class ArticleQueryBuilder
 * @method static RelinkingModelCollection|Collection get()
 * @method static Relinking find()
 * @method static Relinking|null first()
 * @method static RelinkingQueryBuilder whereFromContext($value)
 * @method static RelinkingQueryBuilder whereFromId($value)
 * @method static RelinkingQueryBuilder whereToContext($value)
 * @method static RelinkingQueryBuilder whereToId($value)
 * @method static RelinkingQueryBuilder whereId($value)
 *
 * @package Domain\Articles\QueryBuilders
 */
class RelinkingQueryBuilder extends Builder
{
    /**
     * Вовзвращает массив ID элементов контекста $alias, у которых кол-во входящих ссылок больше чем $count
     *
     * @return array
     */
    public function getIdsWhereCountIncomingLinksInContextMoreThen(string $alias, int $count): array
    {
        return $this->groupBy('to_id')->havingRaw('COUNT(`to_id`) >= ' . $count)->where('to_context', $alias)->pluck('to_id')->toArray();
    }

    /**
     * Возвращает кол-во исходящих ссылок, сгруппированных по контексту и ID
     *
     * @return Collection
     */
    public function getAllLinksCountByPages()
    {
        return $this->selectRaw('from_context, from_id, COUNT(id) as "count"')->groupBy('from_context', 'from_id')->toBase()->get();
    }

    /**
     * Возвращает кол-во исходящих ссылок для определенного элемента контекста
     *
     * @param string $alias
     * @param        $id
     *
     * @return int
     */
    public function getLinksCount(string $alias, $id): int
    {
        return $this->selectRaw('from_context, from_id, COUNT(id) as "count"')->groupBy('from_context', 'from_id')->for($alias, $id)->toBase()->pluck('count')->first() ?? 0;
    }

    /**
     * Возвращает кол-во входящих ссылок, сгруппированных по контексту и ID
     *
     * @return Collection
     */
    public function getAllIncomingLinksCountByPages()
    {
        return $this->selectRaw('to_context, to_id, COUNT(id) as "count"')->groupBy('to_context', 'to_id')->toBase()->get();
    }

    public function whereIncomingPageIs(string $alias, $id): RelinkingQueryBuilder
    {
        return $this->whereToContext($alias)->whereToId($id);
    }

    /**
     * Под определенный контекст и ID
     *
     * @param string $alias
     * @param string $id
     *
     * @return RelinkingQueryBuilder
     */
    public function for(string $alias, string $id): RelinkingQueryBuilder
    {
        return $this->whereToContext($alias)->whereFromId($id);
    }

    /**
     * Под определенный контекст и ID
     *
     * @param string $alias
     * @param string $id
     *
     * @return RelinkingQueryBuilder
     */
    public function whereIncoming(string $alias, string $id)
    {
        return $this->whereToContext($alias)->whereToId($id);
    }

    /**
     * Получает ссылки для контекста
     *
     * @param string $alias
     * @param string $id
     *
     * @return RelinkingModelCollection
     */
    public function getFor(string $alias, string $id): RelinkingModelCollection
    {
        return $this->whereFromContext($alias)->whereFromId($id)->get();
    }

    /**
     * Найти по контексту
     *
     * @param string $context
     * @param string $id
     *
     * @return Relinking|null
     */
    public function findByContext(string $context, string $id): ?Relinking
    {
        return $this->whereContext($context)->whereItemId($id)->first();
    }
}
