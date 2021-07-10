<?php

namespace MasterDmx\LaravelRelinking\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RelinkingSearch
 *
 * @property int linkable_id
 * @property int link_id
 * @property float relevance
 *
 * @package MasterDmx\LaravelRelinking
 */
class Relinking extends Model
{
    public const DB_TABLE = 'mdr_relinking';

    protected $table = self::DB_TABLE;
    protected $fillable = [
        'linkable_id',
        'link_id',
        'relevance'
    ];

    /**
     * New connect
     *
     * @param int   $linkableId
     * @param int   $linkId
     * @param float $relevance
     *
     * @return Relinking
     */
    public static function new(int $linkableId, int $linkId, float $relevance): Relinking
    {
        return static::create([
            'linkable_id' => $linkableId,
            'link_id' => $linkId,
            'relevance' => $relevance,
        ]);
    }

    /**
     * Removes all links
     *
     * @param int $linkableId
     *
     * @return int
     */
    public static function removeLinksFor(int $linkableId): int
    {
        return static::query()->where('linkable_id', $linkableId)->delete();
    }

    /**
     * Removes all links
     *
     * @param int $linksId
     *
     * @return int
     */
    public static function removeReferrersFor(int $linksId): int
    {
        return static::query()->where('link_id', $linksId)->delete();
    }

    public static function removeAllConnectsFor(int $id): void
    {
        static::removeLinksFor($id);
        static::removeReferrersFor($id);
    }
}
