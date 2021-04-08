<?php

namespace MasterDmx\LaravelRelinking\Relevance;

use MasterDmx\LaravelRelinking\Contexts\Context;
use MasterDmx\LaravelRelinking\Contracts\LinkContract;

class Relevance implements LinkContract
{
    /**
     * ID
     *
     * @var string
     */
    private string $id;

    /**
     * Релевантность
     *
     * @var float
     */
    private float $relevance;

    /**
     * Контекст
     *
     * @var Context
     */
    private Context $context;

    /**
     * RelevanceLink constructor.
     *
     * @param string $id
     * @param float  $relevance
     */
    public function __construct(string $id, float $relevance)
    {
        $this->id = $id;
        $this->relevance = $relevance;
    }

    /**
     * Статическая инициализация объекта
     *
     * @param string $id
     * @param float  $relevance
     *
     * @return static
     */
    public static function init(string $id, float $relevance): Relevance
    {
        return new static($id, $relevance);
    }

    /**
     * @param Context $context
     *
     * @return Relevance
     */
    public function setContext(Context $context): Relevance
    {
        $this->context = $context;

        return $this;
    }

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
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getRelevance(): float
    {
        return $this->relevance;
    }
}
