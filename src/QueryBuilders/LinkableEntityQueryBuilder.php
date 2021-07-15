<?php

namespace MasterDmx\LaravelRelinking\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use MasterDmx\LaravelRelinking\Models\LinkableEntity;

/**
 * Class LinkableEntityQueryBuilder
 *
 * @method LinkableEntityQueryBuilder whereLinkableType($value)
 * @method LinkableEntityQueryBuilder whereLinkableId($value)
 * @method static LinkableEntity first()
 * @method static LinkableEntity find(int $id)
 *
 * @package MasterDmx\LaravelRelinking\QueryBuilders
 */
class LinkableEntityQueryBuilder extends Builder
{
    /**
     * Флаг подгрузки линкуемой модели
     *
     * @var bool
     */
    private bool $needLinkable = false;

    /**
     * Подгрузить линкуемые модели
     *
     * @return $this
     */
    public function withLinkable(): static
    {
        $this->needLinkable = true;

        return $this;
    }


    /**
     * Убираем поисковой текст из запроса
     *
     * @return $this
     */
    public function withoutSearchText(): static
    {
        return $this->select('id', 'linkable_type', 'linkable_id', 'created_at', 'updated_at');
    }

    public function whereRelinking(callable $callback): static
    {
        return $this->whereHas('relinking', $callback);
    }

    public function hasRelinking(): static
    {
        return $this->has('relinking');
    }

    public function withRelinking(callable $callback = null): static
    {
        return isset($callback) ? $this->with(['relinking' => $callback]) : $this->with('relinking');
    }

    /**
     * Модернизизуем GET метод для динамической подгрузки отношений с линкуемыми моделями
     *
     * @param array|string|string[] $columns
     *
     * @return EloquentCollection|Collection
     */
    public function get($columns = ['*'])
    {
        $entities = parent::get($columns);

        if ($this->needLinkable){
            foreach ($entities as $entity){
                /** @var LinkableEntity $entity */
                $list[$entity->linkable_type][$entity->linkable_id] = $entity->linkable_id;
            }

            foreach ($list ?? [] as $class => $ids){
                $linkableCollection = app($class)->getLinkableModelsByIds($ids);

                foreach ($entities as $entity){
                    /** @var LinkableEntity $entity */

                    if ($linkable = $linkableCollection->find($entity->linkable_id)) {
                        $entity->setRelation('linkable', $linkable);
                    }
                }
            }
        }

        return $entities;
    }
}
