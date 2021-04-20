<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Support\Collection;
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
     * Максимальное кол-во ссылок для каждого элемента контекста
     *
     * @return int
     */
    public function getLimit(): int;

    /**
     * Поиск элементов для формирования связей
     *
     * @param string $text
     * @param array  $except
     *
     * @return PotentialLinkCollection
     */
    public function search(string $text, array $except): PotentialLinkCollection;

    /**
     * Создает массив контекстов под конкретные элементы контекстов
     *
     * @param array $ids
     *
     * @return Collection
     */
    public function getLinksData(array $ids): Collection;
}
