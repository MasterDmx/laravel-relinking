<?php

namespace MasterDmx\LaravelRelinking\Ligaments;

use Illuminate\Database\Eloquent\Model;
use MasterDmx\LaravelRelinking\Contracts\LinkContract;
use MasterDmx\LaravelRelinking\Links\Contracts\IsLink;
use MasterDmx\LaravelRelinking\Links\Link;
use MasterDmx\LaravelRelinking\Relevance\Relevance;

/**
 * Class Article
 *
 * @property int $id
 * @property string $context
 * @property string $item_id
 * @property int $link_id
 * @property float $relevance
 *
 * @method static LigamentQueryBuilder query()
 * @method LigamentQueryBuilder newQuery()
 *
 * @package Domain\Articles
 */
class Ligament extends Model
{
    protected $table = 'relinking_ligaments';

    /**
     * Добавить связь для ссылки
     *
     * @param int       $linkId
     * @param Relevance $relevance
     *
     * @return $this
     */
    public static function add(int $linkId, Relevance $relevance): Ligament
    {
        $model = new static();
        $model->context = $relevance->getContext()->getAlias();
        $model->item_id = $relevance->getId();
        $model->relevance = $relevance->getRelevance();
        $model->link_id = $linkId;
        $model->save();

        return $model;
    }

    public function link()
    {
        return $this->belongsTo(Link::class, 'id', 'link_id');
    }

    public function newEloquentBuilder($query): LigamentQueryBuilder
    {
        return new LigamentQueryBuilder($query);
    }
}
