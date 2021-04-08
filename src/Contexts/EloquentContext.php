<?php

namespace MasterDmx\LaravelRelinking\Contexts;

use Illuminate\Database\Eloquent\Model;
use MasterDmx\LaravelRelinking\Relevance\Relevance;
use MasterDmx\LaravelRelinking\Relevance\RelevanceCollection;

abstract class EloquentContext implements Context
{
    abstract static public function alias(): string;
    abstract protected function searchModel(): Model;

    protected function searchField(): string
    {
        return 'search';
    }

    protected function searchKey(): string
    {
        return 'id';
    }

    /**
     * Поиск
     *
     * @param string $text
     * @param array  $except
     *
     * @return RelevanceCollection
     */
    public function search(string $text, array $except): RelevanceCollection
    {
        $result = $this->searchModel()->newQuery()
            ->selectRaw($this->searchKey() . ', MATCH(' . $this->searchField() . ') AGAINST("' . $text . '") as relevance')
            ->whereRaw('MATCH(' . $this->searchField() . ' ) AGAINST("' . $text . '")')
            ->whereNotIn($this->searchKey(), $except)
            ->toBase()
            ->get()
            ->map(function ($Item) {
                $key = $this->searchKey();
                return Relevance::init($Item->$key, $Item->relevance);
            });

        return new RelevanceCollection($result);
    }

    /**
     * Получить название
     *
     * @return string
     */
    public function getAlias(): string
    {
        return static::alias();
    }

    public function getName(): string
    {
        return static::alias();
    }

    /**
     * Максимальное кол-во ссылок для каждого элемента контекста
     *
     * @return int
     */
    public function getLimit(): int
    {
        return 3;
    }


}
