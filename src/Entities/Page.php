<?php

namespace MasterDmx\LaravelRelinking\Entities;

use MasterDmx\LaravelRelinking\Context;
use MasterDmx\LaravelRelinking\VO\Link;

class Page
{
    public function __construct(
        private $id,
        private Context $context,
        private Link $link,
        private int $linksCount = 0,
        private int $incomingLinksCount = 0,
    ){ }

    public function getId()
    {
        return $this->id;
    }

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

    /**
     * @return int
     */
    public function getLinksCount(): int
    {
        return $this->linksCount;
    }

    /**
     * @return int
     */
    public function getIncomingLinksCount(): int
    {
        return $this->incomingLinksCount;
    }

    /**
     * Имеет свободные места под исходящие ссылки
     *
     * @return bool
     */
    public function hasFreeLinks(): bool
    {
        return $this->linksCount !== $this->context->linksLimit();
    }

    /**
     * Имеет свободные места под исходящие ссылки
     *
     * @return bool
     */
    public function hasFreeIncomingLinks(): bool
    {
        return $this->incomingLinksCount !== $this->context->incomingLinksLimit();
    }
}
