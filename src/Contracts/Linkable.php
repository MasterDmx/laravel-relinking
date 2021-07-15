<?php

namespace MasterDmx\LaravelRelinking\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use MasterDmx\LaravelRelinking\Models\LinkableEntity;
use MasterDmx\LaravelRelinking\ModelRelinking;

interface Linkable
{
    /**
     * Массив типов, с которым разрешена перелинковка
     *
     * @return array
     */
    public function linkableTypes(): array;

    /**
     * Тип, записывающийся в БД
     *
     * @return string
     */
    public function linkableType(): string;

    /**
     * Id, записывающийся в БД для организации связи
     *
     * @return int
     */
    public function linkableId(): int;

    /**
     * Максимальное кол-во исходящих ссылок
     *
     * @return int
     */
    public function linksLimit(): int;

    /**
     * Масикмальное кол-во входящих ссылок
     *
     * @return int
     */
    public function referrerLimit(): int;

    /**
     * Запрос, на подгрузку данных
     *
     * @return Collection
     */
    public function getLinkableModelsByIds(array $ids): Collection;

    /**
     * Relation to linkable model
     *
     * @return LinkableEntity|null
     */
    public function linkable();

    /**
     * Return LinkableEntity model
     *
     * @return LinkableEntity|null
     */
    public function linkableEntity(): ?LinkableEntity;

    /**
     * Data for create search text
     *
     * @return string
     */
    public function linkableSearchText(): string;

    /**
     * Ручная установка отношения
     *
     * @param LinkableEntity $model
     *
     * @return $this
     */
    public function setLinkableRelation(LinkableEntity $model): static;

    /**
     * Helper
     *
     * @return ModelRelinking
     */
    public function relinking(): ModelRelinking;

    public function allLinkable(): Collection;
}
