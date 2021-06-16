<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

/**
 * Class LinkCollection
 *
 * @method RelinkingModel[] all()
 *
 * @package MasterDmx\LaravelRelinking\Links
 */
class RelinkingModelCollection extends EloquentCollection
{
    /**
     * Разбить коллекцию на разные коллекции, содержащие только ссылки определенного контекста
     *
     * @return RelinkingModelCollection
     */
    public function groupByContext(): RelinkingModelCollection
    {
        return $this->groupBy(function ($item, $key) {
            return $item->from_context;
        });
    }

    /**
     * Разбить коллекцию на разные коллекции, содержащие только ссылки определенного контекста
     *
     * @return RelinkingModelCollection
     */
    public function groupByOutgoingContext(): RelinkingModelCollection
    {
        return $this->groupBy(function ($model, $key) {
            return $model->to_context;
        });
    }

    /**
     * Оставляет в коллекции только модели, с входящим контекстом $context
     *
     * @param string $alias
     *
     * @return RelinkingModelCollection
     */
    public function whereContext($context): RelinkingModelCollection
    {
        if (is_a($context, Context::class)) {
            $context = $context->getAlias();
        }

        return $this->filter(fn ($model) => $model->from_context === $context);
    }

    /**
     * Оставляет в коллекции только модели, где контекст "куда" равен $context
     *
     * @param string|Context $context
     *
     * @return RelinkingModelCollection
     */
    public function whereOutgoingContext($context): RelinkingModelCollection
    {
        if (is_a($context, Context::class)) {
            $context = $context->getAlias();
        }

        return $this->filter(fn ($model) => $model->to_context === $context);
    }

    /**
     * Получить ID
     *
     * @return Collection
     */
    public function getIds(): Collection
    {
        return $this->pluck('from_id');
    }

    /**
     * Получить ID
     *
     * @return Collection
     */
    public function getOutgoingIds(): Collection
    {
        return $this->pluck('to_id');
    }

    /**
     * @param Collection $collection
     *
     * @return RelinkingModelCollection
     */
    public function setLinksData(Collection $collection): RelinkingModelCollection
    {
        foreach ($this->all() as $link){
            $data = $collection->filter(fn ($item) => $item->getId() === $link->context_id)->first();

            if (isset($data)) {
                $link->setData($data);
            }
        }

        return $this;
    }
}
