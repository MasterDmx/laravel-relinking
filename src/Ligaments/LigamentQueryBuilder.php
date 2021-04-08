<?php

namespace MasterDmx\LaravelRelinking\Ligaments;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Class ArticleQueryBuilder
 * @method static Collection|EloquentCollection get()
 * @method static Ligament first()
 * @method static LigamentQueryBuilder whereContext($value)
 * @method static LigamentQueryBuilder whereItemId($value)
 * @method static LigamentQueryBuilder whereId($value)
 * @method static LigamentQueryBuilder whereLinkId($value)
 *
 * @package Domain\Articles\QueryBuilders
 */
class LigamentQueryBuilder extends Builder
{
    public function whereContextAndId($context, $id): LigamentQueryBuilder
    {
        return $this->whereContext($context)->whereItemId($id);
    }

    public function getCountByLink(int $id): int
    {
        return $this->whereLinkId($id)->count();
    }
}
