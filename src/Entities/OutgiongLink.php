<?php

namespace MasterDmx\LaravelRelinking\Entities;

use MasterDmx\LaravelRelinking\Context;
use MasterDmx\LaravelRelinking\VO\Link;

class OutgiongLink
{
    public function __construct(
        private Context $context,
        private Link $link,
        private float $relevance,
    ){}

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->link->getUrl();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->link->getTitle();
    }

    public function getRelevance(): float
    {
        return $this->relevance;
    }
}
