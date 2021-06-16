<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Database\Eloquent\Model;
use MasterDmx\LaravelRelinking\Context;

/**
 * Модель перелинковки
 *
 * @property int $id
 * @property string $from_context
 * @property string $from_id
 * @property string $to_context
 * @property string $to_id
 * @property float $relevance
 *
 * @property-read Context $context
 * @property-read string $url
 * @property-read string $title
 *
 * @method static RelinkingQueryBuilder query()
 * @method RelinkingQueryBuilder newQuery()
 *
 * @package MasterDmx\LaravelRelinking
 */
class RelinkingModel extends Model
{
    protected $table = 'relinking';

    // --------------------------------------------------------
    // Administration
    // --------------------------------------------------------

    /**
     * Регистрирует новую перелинковку
     *
     * @param Context           $context
     * @param string|int        $id
     * @param SelectedRelinking $selected
     *
     * @return Relinking
     */
    public static function register(Context $context, $id, SelectedRelinking $selected): self
    {
        $relinking = new static();
        $relinking->from_context = $context->getAlias();
        $relinking->from_id      = $id;
        $relinking->to_context   = $selected->context->getAlias();
        $relinking->to_id        = $selected->id;
        $relinking->relevance    = $selected->relevance;
        $relinking->save();

        return $relinking;
    }

    /**
     * Регистрирует множество перелинковок
     *
     * @param Context           $context
     * @param string                  $id
     * @param SelectedRelinkingCollection $links
     */
    public static function registerByList(Context $context, string $id, SelectedRelinkingCollection $selected): void
    {
        $selected->each(fn ($item) => static::register($context, $id, $item));
    }

    // --------------------------------------------------------
    // Settings
    // --------------------------------------------------------

    public function newCollection(array $models = []): RelinkingModelCollection
    {
        return new RelinkingModelCollection($models);
    }

    public function newEloquentBuilder($query): RelinkingQueryBuilder
    {
        return new RelinkingQueryBuilder($query);
    }
}
