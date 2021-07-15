<?php

namespace MasterDmx\LaravelRelinking;

use MasterDmx\LaravelRelinking\Contracts\Linkable;
use MasterDmx\LaravelRelinking\Exceptions\LinkableEntityHasAlreadyRegisteredException;
use MasterDmx\LaravelRelinking\Models\LinkableEntity;

class ModelRelinking
{
    public function __construct(
        private Linkable $model,
    ){}

    /**
     * Registers the model in the relinking service
     *
     * @throws \MasterDmx\LaravelRelinking\Exceptions\LinkableEntityHasAlreadyRegisteredException
     */
    public function register(): static
    {
        $this->model->setLinkableRelation(LinkableEntity::register(
            $this->model->linkableType(),
            $this->model->linkableId(),
            $this->model->linkableSearchText()
        ));

        return $this;
    }

    /**
     * Refreshes the search text
     *
     * @return ModelRelinking
     */
    public function edit(): static
    {
        $this->model->linkableEntity()->updateSearchText($this->model->linkableSearchText());

        return $this;
    }

    /**
     * Registers the model in the relinking service, if it is not, or returns, if it is
     *
     * @return static
     */
    public function make(): static
    {
        try {
            $this->register();
        } catch (LinkableEntityHasAlreadyRegisteredException $e) {
            $this->model->linkableEntity();
        }

        return $this;
    }

    /**
     * Generates links
     * Returns yes TRUE at least one new relationship has been added
     *
     * @return bool
     */
    public function generate(): bool
    {
        return $this->model->linkableEntity()?->generate() ?? false;
    }

    /**
     * Removes all links and regenerates them
     *
     * @return bool
     */
    public function regenerate(): static
    {
        $this->clearLinks();
        $this->generate();

        return $this;
    }

    /**
     * Returns a collection of links
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getLinks()
    {
        return $this->model->linkableEntity()->getLinks();
    }

    /**
     * Removes all anchored links
     */
    public function clearLinks(): static
    {
        $this->model->linkableEntity()->clearLinks();

        return $this;
    }

    /**
     * Removes all referrers
     */
    public function clearReferrers(): static
    {
        $this->model->linkableEntity()->clearReferrers();

        return $this;
    }

    /**
     * Removes all connects
     */
    public function clear(): static
    {
        $this->model->linkableEntity()->clear();

        return $this;
    }

    /**
     * Removes all connects
     */
    public function remove(): static
    {
        $this->model->linkableEntity()?->remove();

        return $this;
    }
}
