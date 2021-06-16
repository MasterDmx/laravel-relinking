<?php

namespace MasterDmx\LaravelRelinking;

class SelectedRelinking
{
    public Context $context;

    public string $id;

    public float $relevance;

    public function __construct(Context $context, string $id, float $relevance)
    {
        $this->context = $context;
        $this->id = $id;
        $this->relevance = $relevance;
    }
}
