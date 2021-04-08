<?php

namespace MasterDmx\LaravelRelinking\Contexts;

use MasterDmx\LaravelRelinking\Relevance\RelevanceCollection;

interface Context
{
    static public function alias(): string;

    public function getAlias(): string;

    public function getName(): string;

    public function search(string $text, array $except): RelevanceCollection;

    /**
     * Максимальное кол-во ссылок для каждого элемента контекста
     *
     * @return int
     */
    public function getLimit(): int;

    /**
     * Максимальное кол-во ссылок для каждого элемента контекста
     *
     * @return array
     */
    public function saturateCollection(array $ids): array;
}
