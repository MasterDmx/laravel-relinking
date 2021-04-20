<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Database\Eloquent\Model;
use MasterDmx\LaravelRelinking\Context;
use MasterDmx\LaravelRelinking\ContextRegistry;
use MasterDmx\LaravelRelinking\Links\LinkContextCast;

/**
 * Модель перелинковки
 *
 * @property int $id
 * @property string $for_context_alias
 * @property string $for_context_id
 * @property string $context_alias
 * @property string $context_id
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
class Relinking extends Model
{
    protected $table = 'relinking';
    protected Context $contextInstance;
    protected LinkData $data;

    /**
     * Устанавливает объект данных
     *
     * @param LinkData $data
     */
    public function setData(LinkData $data)
    {
        $this->data = $data;
    }

    /**
     * Возвращает объект данных
     *
     * @return LinkData
     */
    public function getData(): LinkData
    {
        return $this->data;
    }

    public function toArray()
    {
        $data = parent::toArray();
        $data['url'] = $this->url;
        $data['title'] = $this->title;
        $data['data'] = $this->getData()->toArray();

        return $data;
    }

    // --------------------------------------------------------
    // Mutators
    // --------------------------------------------------------

    /**
     * Объект контекста
     *
     * @return Context
     */
    public function getContextAttribute(): Context
    {
        if (isset($this->contextInstance)){
            return $this->contextInstance;
        }

        return app(ContextRegistry::class)->get($this->context_alias);
    }

    /**
     * URL
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return $this->getData()->getUrl();
    }

    /**
     * Анкор
     *
     * @return string
     */
    public function getTitleAttribute(): string
    {
        return $this->getData()->getTitle();
    }

    // --------------------------------------------------------
    // Administration
    // --------------------------------------------------------

    /**
     * Регистрирует новую перелинковку
     *
     * @param string        $alias
     * @param string        $id
     * @param PotentialLink $link
     *
     * @return Relinking
     */
    public static function register(string $alias, string $id, PotentialLink $link): Relinking
    {
        $relinking = new static();
        $relinking->for_context_alias = $alias;
        $relinking->for_context_id    = $id;
        $relinking->context_alias     = $link->getContext()->getAlias();
        $relinking->context_id        = $link->getId();
        $relinking->relevance         = $link->getRelevance();
        $relinking->save();

        return $relinking;
    }

    /**
     * Регистрирует множество перелинковок
     *
     * @param string                  $alias
     * @param string                  $id
     * @param PotentialLinkCollection $links
     */
    public static function registerByList(string $alias, string $id, PotentialLinkCollection $links): void
    {
        $links->each(fn ($link) => static::register($alias, $id, $link));
    }

    // --------------------------------------------------------
    // Settings
    // --------------------------------------------------------

    public function newCollection(array $models = []): RelinkingCollection
    {
        return new RelinkingCollection($models);
    }

    public function newEloquentBuilder($query): RelinkingQueryBuilder
    {
        return new RelinkingQueryBuilder($query);
    }
}
