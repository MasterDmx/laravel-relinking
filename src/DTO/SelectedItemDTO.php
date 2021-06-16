<?php

namespace MasterDmx\LaravelRelinking\DTO;

class SelectedItemDTO
{
    public string $id;

    public float $relevance;

    public function __construct(string $id, float $relevance)
    {
        $this->id = $id;
        $this->relevance = $relevance;
    }
}
