<?php

namespace MasterDmx\LaravelRelinking\Links;

use Illuminate\Database\Eloquent\Model;
use MasterDmx\LaravelRelinking\Contexts\Context;
use MasterDmx\LaravelRelinking\Contracts\LinkContract;
use MasterDmx\LaravelRelinking\Ligaments\Ligament;
use MasterDmx\LaravelRelinking\Relevance\RelevanceCollection;
use MasterDmx\LaravelRelinking\RelinkingManager;

/**
 * Class Article
 *
 * @property int $id
 * @property Context $context
 * @property string $item_id
 * @property int $ligaments_count
 * @property string $title
 * @property string $url
 * @method static LinkQueryBuilder query()
 *
 * @package Domain\Articles
 */
class Link extends Model implements LinkContract
{
    public const RELATIONSHIP_LIGAMENTS = 'ligaments';

    protected $table = 'relinking_links';

    protected $casts = [
        'context' => LinkContextCast::class
    ];

    public function getContextAlias(): string
    {
        return $this->context;
    }

    public function getId(): string
    {
        return $this->item_id;
    }

    /**
     * Проверить имеются ли свободные связи
     *
     * @return bool
     */
    public function hasFreeLigaments(): bool
    {
        return Ligament::query()->getCountByLink($this->id) < $this->context->getLimit();
    }

    /**
     * Проверяет наличие свободных связей по кол-ву имеющихся связей
     */
    public function hasFreeLigamentsByCount(): bool
    {
        return ($item->ligaments_count ?? 0) >= $this->context->getLimit();
    }

    public function getLigaments()
    {
        return $this->ligaments()->get();
    }

    public function getLigamentsCount(): int
    {
        return $this->ligaments_count ?? 0;
    }

    public function getLigamentsLimit(): int
    {
        return $this->context->getLimit();
    }

    /**
     * Установить связи
     *
     * @param RelevanceCollection $relevances
     */
    public function setLigaments(RelevanceCollection $relevances): void
    {
        foreach ($relevances->all() as $link) {
            Ligament::add($this->id, $link);
        }
    }

    public function getContext(): Context
    {
        return  $this->context;
    }

    public function getUrl()
    {
        return $this->url ?? '';
    }

    public function getTitle()
    {
        return $this->title ?? '';
    }

    /**
     * Добавить ссылку
     *
     * @param string $context
     * @param string $id
     *
     * @return static
     */
    public static function add(string $context, string $id): Link
    {
        $model = new static();
        $model->context = $context;
        $model->item_id = $id;
        $model->save();

        return $model;
    }

    /**
     * Удалить ссылку
     *
     * @throws \Exception
     */
    public function remove(): void
    {
        if ($this->delete()) {
            Ligament::query()->whereLinkId($this->id)->delete();
            Ligament::query()->whereContextAndId($this->getContextAlias(), $this->getId())->delete();
        }
    }

    public function saturate(LinkSaturateDTO $saturateDTO)
    {
        $this->title = $saturateDTO->getTitle();
        $this->url = $saturateDTO->getUrl();
    }

    // --------------------------------------------------------
    // Relationships
    // --------------------------------------------------------

    /**
     * Get the comments for the blog post.
     */
    public function ligaments()
    {
        return $this->hasMany(Ligament::class, 'link_id', 'id');
    }

    // --------------------------------------------------------
    // Settings
    // --------------------------------------------------------

    public function newCollection(array $models = []): LinkCollection
    {
        return new LinkCollection($models);
    }

    public function newEloquentBuilder($query): LinkQueryBuilder
    {
        return new LinkQueryBuilder($query);
    }
}
