<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MasterDmx\LaravelRelinking\Context;
use MasterDmx\LaravelRelinking\DefaultLinkData;
use MasterDmx\LaravelRelinking\LinkData;
use MasterDmx\LaravelRelinking\LinkDataModel;
use MasterDmx\LaravelRelinking\PotentialLink;
use MasterDmx\LaravelRelinking\PotentialLinkCollection;
use function collect;

abstract class EloquentContext implements Context
{
    /**
     * Обозначение контекста
     *
     * @return string
     */
    abstract static public function alias(): string;

    /**
     * Объект модели для использования
     *
     * @return Model
     */
    abstract protected function model(): Model;

    /**
     * Получить название
     *
     * @return string
     */
    public function getAlias(): string
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

    // -----------------------------------------------------
    // Search
    // -----------------------------------------------------

    /**
     * Модель для поиска
     *
     * @return Model
     */
    protected function searchModel(): Model
    {
        return $this->model();
    }

    /**
     * Поиск
     *
     * @param string $text
     * @param array  $except
     *
     * @return PotentialLinkCollection
     */
    public function search(string $text, array $except): PotentialLinkCollection
    {
        $keyId   = property_exists($this->searchModel(), 'relinkingSearchKeyId') ? $this->model()->relinkingSearchKeyId : 'id';
        $keyText = property_exists($this->searchModel(), 'relinkingSearchKeyText') ? $this->model()->relinkingSearchKeyText : 'search';

        $result = $this->searchModel()->newQuery()
            ->selectRaw($keyId . ', MATCH(' . $keyText . ') AGAINST("' . $text . '") as relevance')
            ->whereRaw('MATCH(' . $keyText . ' ) AGAINST("' . $text . '")')
            ->whereNotIn($keyId, $except)
            ->toBase()
            ->get()
            ->map(fn($item) => PotentialLink::init($item->$keyId, $item->relevance));

        return new PotentialLinkCollection($result);
    }

    // -----------------------------------------------------
    // Data
    // -----------------------------------------------------

    /**
     * @param array
     *
     * @return Collection
     */
    public function getLinksData(array $ids): Collection
    {
        $result = [];

        foreach ($this->prepareData($ids) as $prepared) {
            $result[] = $this->init($prepared);
        }

        return collect($result);
    }

    /**
     * @param LinkDataModel $data
     *
     * @return LinkData
     */
    protected function init($data): LinkData
    {
        return new DefaultLinkData($data->relinkingId(), $data->relinkingUrl(), $data->relinkingTitle());
    }

    /**
     * @param array $ids
     *
     * @return Collection
     */
    protected function prepareData(array $ids)
    {
        $keyId  = property_exists($this->model(), 'relinkingKeyId') ? $this->model()->relinkingKeyId : 'id';
        $fields = property_exists($this->model(), 'relinkingAttributes') ? $this->model()->relinkingAttributes : [];
        $query  = $this->model()->whereIn($keyId, $ids);

        if (!empty($fields)) {
            $query = $query->select($fields);
        }

        return $query->get();
    }

}
