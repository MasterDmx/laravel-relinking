<?php

namespace MasterDmx\LaravelRelinking\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use MasterDmx\LaravelRelinking\Models\LinkableEntity;
use MasterDmx\LaravelRelinking\ModelRelinking;

/**
 * Trait HasRelinking
 *
 * @property-read LinkableEntity $linkable
 *
 * @package MasterDmx\LaravelRelinking\Traits
 */
trait HasRelinking
{
    public function linkableType(): string
    {
        return __CLASS__;
    }

    public function linkableTypes(): array
    {
        return [
            $this->linkableType()
        ];
    }

    public function linkableId(): int
    {
        return $this->id;
    }

    public function linkableEntity(): ?LinkableEntity
    {
        return $this->linkable;
    }

    /**
     * Ограничение на кол-во ссылок
     *
     * @return int
     */
    public function linksLimit(): int
    {
        return 10;
    }

    /**
     * Ограничение на кол-во ссылок, ссылающихся на текущую сущность
     *
     * @return int
     */
    public function referrerLimit(): int
    {
        return 10;
    }

    /**
     * Запрос на получение данных для формирования ссылки
     *
     * @return Builder
     */
    public function linkableQuery(): Builder
    {
        return $this->newQuery();
    }

    public function setLinkableRelation(LinkableEntity $model): static
    {
        return $this->setRelation('linkable', $model);
    }

    /**
     * Relation to linkable model
     *
     * @return LinkableEntity|null
     */
    public function linkable()
    {
        return $this->morphOne(LinkableEntity::class, 'linkable');
    }

    public function allLinkable(): Collection
    {
        return $this->with('linkable')->get();
    }

    /**
     * Relinking manager for model
     *
     * @return ModelRelinking
     */
    public function relinking(): ModelRelinking
    {
        return new ModelRelinking($this);
    }
}
