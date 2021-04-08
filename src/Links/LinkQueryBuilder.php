<?php

namespace MasterDmx\LaravelRelinking\Links;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class ArticleQueryBuilder
 * @method static LinkCollection|Collection get()
 * @method static Link find()
 * @method static Link|null first()
 * @method static LinkQueryBuilder whereContext($value)
 * @method static LinkQueryBuilder whereItemId($value)
 * @method static LinkQueryBuilder whereId($value)
 *
 * @package Domain\Articles\QueryBuilders
 */
class LinkQueryBuilder extends Builder
{
    /**
     * @param string|callable $value
     *
     * @return LinkQueryBuilder
     */
    public function whereLigaments(callable $value): LinkQueryBuilder
    {
        return $this->whereHas(Link::RELATIONSHIP_LIGAMENTS, $value);
    }

    /**
     * @return LinkQueryBuilder
     */
    public function hasLigaments(): LinkQueryBuilder
    {
        return $this->has(Link::RELATIONSHIP_LIGAMENTS);
    }

    /**
     * @param callable|null $callback
     *
     * @return LinkQueryBuilder
     */
    public function withLigaments(?callable $callback = null): LinkQueryBuilder
    {
        return isset($callback) ? $this->with([Link::RELATIONSHIP_LIGAMENTS => $callback]) : $this->with(Link::RELATIONSHIP_LIGAMENTS);
    }

    public function withCountLigaments(): LinkQueryBuilder
    {
        return $this->withCount(Link::RELATIONSHIP_LIGAMENTS);
    }

    public function whereLigamentsContextAndId($context, $id): LinkQueryBuilder
    {
        return $this->whereLigaments(
            fn ($q) => $q->whereContextAndId($context, $id)
        );
    }

    /**
     * Получить кол-во связей всех ссылок
     *
     * @return Collection|LinkCollection
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
     * @return Link|null
     */
    public function findByContext(string $context, string $id): ?Link
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
