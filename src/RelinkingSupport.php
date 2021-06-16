<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Support\Collection;
use MasterDmx\LaravelRelinking\Collections\IncomingLinkCollection;
use MasterDmx\LaravelRelinking\Collections\OutgiongLinkCollection;
use MasterDmx\LaravelRelinking\Entities\IncomingLink;
use MasterDmx\LaravelRelinking\Entities\OutgiongLink;
use MasterDmx\LaravelRelinking\VO\Link;

class RelinkingSupport
{
    public function __construct(
        private ContextManager $contexts
    ){}

    public function getIncomingLinksByRelinkingCollection(RelinkingModelCollection $collection)
    {
        $result = [];

        foreach ($collection->groupByContext()->all() as $alias => $subCollection){
            $data = $this->contexts->get($alias)->getDataForIds($subCollection->getIds())->keyBy(fn($el) => $el->id);

            foreach ($subCollection->all() as $model){
                $link = null;

                foreach ($data as $point){
                    if ($point->id === $model->from_id) {
                        $link = Link::fromContextDTO($point);
                    }
                }

                if (!isset($link)){
                    continue;
                }

                $result[] = new IncomingLink($this->contexts->get($model->to_context), $link, $model->relevance);
            }
        }

        return new IncomingLinkCollection($result);
    }

    public function getLinksByRelinkingCollection(RelinkingModelCollection $collection): OutgiongLinkCollection
    {
        $result = [];

        foreach ($collection->groupByOutgoingContext()->all() as $alias => $subCollection){
            $data = $this->contexts->get($alias)->getDataForIds($subCollection->getOutgoingIds())->keyBy(fn($el) => $el->id);

            foreach ($subCollection->all() as $model){
                $link = null;

                foreach ($data as $point){
                    if ($point->id === $model->to_id) {
                        $link = Link::fromContextDTO($point);
                    }
                }

                if (!isset($link)){
                    continue;
                }

                $result[] = new OutgiongLink($this->contexts->get($model->to_context), $link, $model->relevance);
            }
        }

        return new OutgiongLinkCollection($result);
    }


}
