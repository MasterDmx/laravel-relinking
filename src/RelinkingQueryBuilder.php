<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use MasterDmx\LaravelRelinking\Links\Relinking;
use MasterDmx\LaravelRelinking\RelinkingCollection;

/**
 * Class ArticleQueryBuilder
 * @method static RelinkingCollection|Collection get()
 * @method static Relinking find()
 * @method static Relinking|null first()
 * @method static RelinkingQueryBuilder whereForContextAlias($value)
 * @method static RelinkingQueryBuilder whereForContextId($value)
 * @method static RelinkingQueryBuilder whereContextAlias($value)
 * @method static RelinkingQueryBuilder whereContextId($value)
 * @method static RelinkingQueryBuilder whereId($value)
 *
 * @package Domain\Articles\QueryBuilders
 */
class RelinkingQueryBuilder extends Builder
{
    /**
     * @param string|callable $value
     *
     * @return RelinkingQueryBuilder
     */
    public function whereLigaments(callable $value): RelinkingQueryBuilder
    {
        return $this->whereHas(Relinking::RELATIONSHIP_LIGAMENTS, $value);
    }

    /**
     * @return RelinkingQueryBuilder
     */
    public function hasLigaments(): RelinkingQueryBuilder
    {
        return $this->has(Relinking::RELATIONSHIP_LIGAMENTS);
    }

    /**
     * Под определенный контекст и ID
     *
     * @param string $alias
     * @param string $id
     *
     * @return RelinkingQueryBuilder
     */
    public function for(string $alias, string $id)
    {
        return $this->whereForContextAlias($alias)->whereForContextId($id);
    }

    /**
     * Получает ссылки для контекста
     *
     * @param string $alias
     * @param string $id
     *
     * @return RelinkingCollection
     */
    public function getFor(string $alias, string $id): RelinkingCollection
    {
        return $this->whereForContextAlias($alias)->whereForContextId($id)->get();
    }

    public function getIds()
    {
        return $this->pluck('item_id');
    }

    /**
     * Получить кол-во связей всех ссылок
     *
     * @return Collection|RelinkingCollection
     */
    public function getAllCountLigaments()
    {
        return $this->select('id', 'context', 'item_id')->withCountLigaments()->get();
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

    /**
     * Проверить существование
     *
     * @param string $context
     * @param string $id
     *
     * @return bool
     */
    public function checkExists(string $context, string $id): bool
    {
        return $this->whereContext($context)->whereItemId($id)->exists();
    }
}
