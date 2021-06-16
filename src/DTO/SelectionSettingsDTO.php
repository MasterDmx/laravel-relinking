<?php

namespace MasterDmx\LaravelRelinking\DTO;

use Illuminate\Database\Eloquent\Model;

class SelectionSettingsDTO
{
    /**
     * Модель, по которой будет выполняться подбор
     *
     * @var Model
     */
    public Model $model;

    /**
     * Название колонки, которая содержит в себе идентификатор
     *
     * @var string
     */
    public string $columnId;

    /**
     * Название колонки, которая содержит в себе текст для сравнения
     *
     * @var string
     */
    public string $columnSearchText;
}
