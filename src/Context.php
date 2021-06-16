<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Support\Collection;
use MasterDmx\LaravelRelinking\DTO\ContextDTO;
use MasterDmx\LaravelRelinking\PotentialLinkCollection;

interface Context
{
    /**
     * Обозначение контекста
     *
     * @return string
     */
    static public function alias(): string;

    /**
     * Обозначение контекста
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Поиск элементов для формирования связей
     *
     * @param string $text
     * @param array  $except
     *
     * @return SelectedRelinkingCollection
     */
    public function select(string $text, array $except = []): SelectedRelinkingCollection;

    public function getSearchText($id): string;

    /**
     * Создает массив контекстов под конкретные элементы контекстов
     *
     * @param array $ids
     *
     * @return Collection
     */
    public function getLinks(array $ids): Collection;

    public function getDataById($id): ContextDTO;
    public function linksLimit(): int;
    public function incomingLinksLimit(): int;

    public function all();
}
