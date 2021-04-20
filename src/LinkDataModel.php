<?php

namespace MasterDmx\LaravelRelinking;

interface LinkDataModel
{
    /**
     * ID
     *
     * @return string
     */
    public function relinkingId(): string;

    /**
     * Url ссылки
     *
     * @return string
     */
    public function relinkingUrl(): string;

    /**
     * Анкор ссылки
     *
     * @return string
     */
    public function relinkingTitle(): string;
}
