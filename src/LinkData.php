<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Contracts\Support\Arrayable;

interface LinkData extends Arrayable
{
    public function getId(): string;
    public function getTitle(): string;
    public function getUrl(): string;
}
