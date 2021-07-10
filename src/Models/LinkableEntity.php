<?php

namespace MasterDmx\LaravelRelinking\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use MasterDmx\LaravelRelinking\Contracts\Linkable;
use MasterDmx\LaravelRelinking\Exceptions\LinkableEntityHasAlreadyRegisteredException;
use MasterDmx\LaravelRelinking\QueryBuilders\LinkableEntityQueryBuilder;
use MasterDmx\LaravelRelinking\LinkableRegistry;
use MasterDmx\LaravelRelinking\Services\TextCleaner;

/**
 * Class RelinkingSearch
 *
 * @property int                id
 * @property string             linkable_type
 * @property int                linkable_id
 * @property string             search
 * @property string             created_at
 * @property string             updated_at
 * @property Linkable           linkable
 * @property EloquentCollection relinking
 *
 * @method static LinkableEntityQueryBuilder query()
 * @method LinkableEntityQueryBuilder newQuery()()
 *
 * @package MasterDmx\LaravelRelinking
 */
class LinkableEntity extends Model
{
    public const DB_TABLE = 'mdr_linkable_entities';

    protected $table = self::DB_TABLE;

    /**
     * Генерирует ссылки
     * Возвращает кол-во созданных связей
     *
     * @return int
     */
    public function generate(): int
    {
        // Получаем текущий контекст
        $context = LinkableRegistry::get($this->linkable_type);

        // Исключаем ID текущей сущности
        $excludedIds = new Collection($this->id);

        // Кол-во ссылок
        $linksIds = $this->relinking()->pluck('link_id');

        if ($linksIds->count() < $context->linksLimit()) {
            $excludedIds = $excludedIds->merge($linksIds);

            // Загружаем реферерсы, у которых кол-во связей больше 1
            $referrers = DB::table(static::DB_TABLE)
                ->select('id', 'linkable_type')
                ->selectRaw('COUNT(' . Relinking::DB_TABLE.'.link_id' . ') as count')
                ->leftJoin(Relinking::DB_TABLE,static::DB_TABLE.'.id', '=', Relinking::DB_TABLE.'.link_id')
                ->whereIn('id', function ($q) use ($context) {
                    /** @var Builder $q */
                    $q->select('id')->whereIn('linkable_type', $context->linkableTypes());
                })
                ->groupBy(static::DB_TABLE.'.id')
                ->having('count', '>=', 1)
                ->get();

            // Исключаем переполненные связи
            if ($referrers->isNotEmpty()){
                foreach ($referrers as $referrer){
                    $subContext = LinkableRegistry::get($referrer->linkable_type);

                    if ($referrer->count >= $subContext->referrerLimit()) {
                        $excludedIds->add($referrer->id);
                    }
                }
            }

            $selected = $this->newQuery()
                ->selectRaw('id, linkable_type, linkable_id, MATCH(search) AGAINST("' . $this->search . '") as relevance')
                ->whereRaw('MATCH(search) AGAINST("' . $this->search . '")')
                ->whereNotIn('id', $excludedIds)
                ->whereIn('id', function ($q) use ($context) {
                    /** @var Builder $q */
                    $q->select('id')->whereIn('linkable_type', $context->linkableTypes());
                })
                ->toBase()
                ->get();

            if ($selected->isNotEmpty()) {
                $selected
                    ->sortBy(fn ($item) => -$item->relevance)
                    ->take($context->linksLimit() - $linksIds->count())
                    ->each(function ($item) {
                        $this->bindLink($item->id, $item->relevance);
                    });

                return true;
            }
        }

        return 0;
    }

    /**
     * Generates links
     * Returns yes TRUE at least one new relationship has been added
     *
     * @return bool
     */
    public function regenerate()
    {
        $this->clearLinks();

        return $this->generate();
    }

    /**
     * Возвращает коллекцию привязанных ссылок
     *
     * @return EloquentCollection|Collection
     */
    public function getLinks()
    {
        return $this->newQuery()
            ->withoutSearchText()
            ->withLinkable()
            ->whereIn('id', function ($q) {
                /** @var Builder $q */
                $q->from(Relinking::DB_TABLE)->select('link_id')->where('linkable_id', $this->id);
            })
            ->get();
    }

    /**
     * Удаляет все привязанные ссылки
     *
     * @return int
     */
    public function clearLinks(): int
    {
        return Relinking::removeLinksFor($this->id);
    }

    /**
     * Удаляет все рефееры
     *
     * @return int
     */
    public function clearReferrers(): int
    {
        return Relinking::removeReferrersFor($this->id);
    }

    /**
     * Удаляет все связи
     */
    public function clear(): void
    {
        Relinking::removeAllConnectsFor($this->id);
    }

    /**
     * Удаляет привязку модели к сервис и все связи
     *
     * @return bool|null
     */
    public function remove(): ?bool
    {
        $this->clear();

        return $this->delete();
    }

    /**
     * Регистрирует новую сущность
     *
     * @param string $linkableType
     * @param int    $linkableId
     * @param string $text
     *
     * @return static
     * @throws LinkableEntityHasAlreadyRegisteredException
     */
    public static function register(string $linkableType, int $linkableId, string $text): static
    {
        $model = new static();

        $model->linkable_type = $linkableType;
        $model->linkable_id   = $linkableId;
        $model->search        = static::clearText($text);

        if (static::query()->whereLinkableType($model->linkable_type)->whereLinkableId($model->linkable_id)->exists()){
            throw new LinkableEntityHasAlreadyRegisteredException();
        }

        $model->save();

        return $model;
    }

    /**
     * Изменить поисковой текст
     *
     * @param string $text
     *
     * @return LinkableEntity
     */
    public function updateSearchText(string $text): static
    {
        $this->search = static::clearText($text);
        $this->save();

        return $this;
    }

    // -------------------------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------------------------

    /**
     * Добавляет перелинковку для сущности
     *
     * @param int   $id
     * @param float $relevance
     *
     * @return Relinking
     */
    private function bindLink(int $id, float $relevance): Relinking
    {
        return Relinking::new($this->id, $id, $relevance);
    }

    /**
     * Очищает поисковой текст от всего лишнего
     *
     * @param string $text
     *
     * @return string
     */
    private static function clearText(string $text): string
    {
        return app(TextCleaner::class)->clear($text);
    }

    // -------------------------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------------------------

    public function relinking()
    {
        return $this->hasMany(Relinking::class, 'linkable_id');
    }

    public function linkable()
    {
        return $this->morphTo();
    }

    // -------------------------------------------------------------------------------------------
    // Settings
    // -------------------------------------------------------------------------------------------

    public function newEloquentBuilder($query): LinkableEntityQueryBuilder
    {
        return new LinkableEntityQueryBuilder($query);
    }
}
