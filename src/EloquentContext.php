<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MasterDmx\LaravelRelinking\Context;
use MasterDmx\LaravelRelinking\DefaultLinkData;
use MasterDmx\LaravelRelinking\DTO\ContextDTO;
use MasterDmx\LaravelRelinking\DTO\LinkDTO;
use MasterDmx\LaravelRelinking\DTO\SelectedItemDTO;
use MasterDmx\LaravelRelinking\DTO\SelectionSettingsDTO;
use MasterDmx\LaravelRelinking\Exceptions\ContextItemNotFoundException;
use function collect;

abstract class EloquentContext implements Context
{
    /**
     * Получить название
     *
     * @return string
     */
    public function getAlias(): string
    {
        return static::alias();
    }

    // -----------------------------------------------------
    // Search
    // -----------------------------------------------------

    /**
     * Поиск похожих сущностей
     * Задача вернуть DTO, содержащий в себе Relevance и ID
     *
     * @param string $text
     * @param array  $except
     *
     * @return SelectedRelinkingCollection
     */
    public function select(string $text, array $except = []): SelectedRelinkingCollection
    {
        $settings = $this->selectionSettings();

        return new SelectedRelinkingCollection($settings->model->newQuery()
            ->selectRaw($settings->columnId . ', MATCH(' . $settings->columnSearchText . ') AGAINST("' . $text . '") as relevance')
            ->whereRaw('MATCH(' . $settings->columnSearchText . ' ) AGAINST("' . $text . '")')
            ->whereNotIn($settings->columnId, $except)
            ->toBase()
            ->get()
            ->map(function ($item) use ($settings) {
                $id = $settings->columnId;

                return $this->initSelectedRelinking($item->$id, $item->relevance);
            }));
    }

    public function getSearchText($id): string
    {
        $item = $this->selectionSettings()->model->newQuery()->select($this->selectionSettings()->columnSearchText)->where($this->selectionSettings()->columnId, $id)->toBase()->first();

        throw_if(!isset($item), new ContextItemNotFoundException('Context item #' . $id . ' in context ' . $this->getAlias() . ' not found'));

        return $item->search;
    }

    protected function initSelectedRelinking(string $id, float $relevance): SelectedRelinking
    {
        return new SelectedRelinking($this, $id, $relevance);
    }

    public function getAll()
    {
        return $this->model()->all();
    }

    public function all()
    {
        $fields = $this->selectableFields();
        $id = $this->columnId();

        return $this->model()->select(!empty($fields) ? $fields : '*')->get()->map(function ($model) use ($id) {
            return new ContextDTO(
                $model->$id,
                $this->url($model),
                $this->title($model)
            );
        });
    }

    public function getDataById($id): ContextDTO
    {
        $fields = $this->selectableFields();
        $columnId = $this->columnId();
        $model = $this->model()->select(!empty($fields) ? $fields : '*')->where($columnId, $id)->first();

        return new ContextDTO(
            $model->$columnId,
            $this->url($model),
            $this->title($model)
        );
    }

    public function getDataForIds($ids)
    {
        if (is_a($ids, Collection::class)) {
            $ids = $ids->toArray();
        }

        $fields = $this->selectableFields();
        $columnId = $this->columnId();

        return $this->model()->select(!empty($fields) ? $fields : '*')->whereIn($columnId, $ids)->get()->map(function ($model) use ($columnId) {
            return new ContextDTO(
                $model->$columnId,
                $this->url($model),
                $this->title($model)
            );
        });
    }

    /**
     * Возвращает коллекцию LinkDTO
     *
     * @param array $ids
     *
     * @return Collection
     */
    public function getLinks(array $ids): Collection
    {
        $fields   = $this->selectableFields();
        $columnId = $this->columnId();
        $result   = [];

        return $this->model()
            ->select(!empty($fields) ? $fields : '*')
            ->whereIn($columnId, $ids)
            ->get()
            ->map(fn($el) => new LinkDTO($el->$columnId, $this->initLink($el)));
    }

    abstract protected function initLink($item): Link;

    /**
     * Настройки для метода подбора
     *
     * @return SelectionSettingsDTO
     */
    protected function selectionSettings(): SelectionSettingsDTO
    {
        $dto = new SelectionSettingsDTO();

        $dto->model            = $this->model();
        $dto->columnId         = 'id';
        $dto->columnSearchText = 'search';

        return $dto;
    }

    abstract protected function url($model): string;
    abstract protected function title($model): string;

    // ----------------------------------------------------------
    // Settings
    // ----------------------------------------------------------

    /**
     * Обозначение контекста
     *
     * @return string
     */
    abstract static public function alias(): string;

    /**
     * Модели для использования
     *
     * @return Model
     */
    abstract protected function model(): Model;

    /**
     * Название колонки идентификатора
     *
     * @return string
     */
    protected function columnId(): string
    {
        return 'id';
    }

    /**
     * Загружаемые поля при получении ссылок
     *
     * @return array
     */
    protected function selectableFields(): array
    {
        return [];
    }

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
     * Название колонки идентификатора модели поиска
     *
     * @return string|id
     */
    protected function searchColumnId(): string
    {
        return $this->columnId();
    }

    /**
     * Название колонки c текстом для поиска
     *
     * @return string|id
     */
    protected function searchColumnText(): string
    {
        return 'search';
    }

    /**
     * Максимальное кол-во исходящих ссылок
     *
     * @return int
     */
    public function linksLimit(): int
    {
        return 4;
    }

    /**
     * Максимальное кол-во входящих ссылок
     *
     * @return int
     */
    public function incomingLinksLimit(): int
    {
        return 3;
    }
}
