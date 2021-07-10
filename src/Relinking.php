<?php

namespace MasterDmx\LaravelRelinking;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use MasterDmx\LaravelRelinking\Contracts\Linkable;
use MasterDmx\LaravelRelinking\Models\LinkableEntity;
use MasterDmx\LaravelRelinking\Models\Relinking as RelinkingModel;

/**
 * Class RelinkingSearch
 *
 * @property int                linkable_id
 * @property int                link_id
 *
 * @method static Builder query()
 * @method Builder newQuery()()
 *
 * @package MasterDmx\LaravelRelinking
 */
class Relinking
{
    /**
     * Регистрирует новую сущность для перелинковки
     *
     * @param string $type
     * @param int    $id
     * @param string $text
     *
     * @return LinkableEntity
     * @throws Exceptions\LinkableEntityHasAlreadyRegisteredException
     */
    public function register(string $type, int $id, string $text): LinkableEntity
    {
        return LinkableEntity::register($type, $id, $text);
    }

    /**
     * Возвращает модель сущности перелинковки по ее системному ID
     *
     * @param int $id
     *
     * @return LinkableEntity|null
     */
    public function find(int $id): ?LinkableEntity
    {
        return LinkableEntity::query()->find($id);
    }

    /**
     * Возвращает ссылаемую сущность по типу и id
     *
     * @param string $type
     * @param int    $id
     *
     * @return LinkableEntity|null
     */
    public function getBy(string $type, int $id): ?LinkableEntity
    {
        return LinkableEntity::query()->whereLinkableType($type)->whereLinkableId($id)->first();
    }

    /**
     * Отвязывает все модели и удаляет все связи
     */
    public function reset(): static
    {
        LinkableEntity::query()->truncate();

        return $this->clear();
    }

    /**
     * Удаляет все связи
     *
     * @return $this
     */
    public function clear(): static
    {
        RelinkingModel::query()->truncate();

        return $this;
    }

    /**
     * Удаляет все имеющиеся связи и генерирует их заного
     *
     * @return $this
     */
    public function regenerate()
    {
        $this->clear();
        $this->generate();

        return $this;
    }

    /**
     * Генерирует все связи автоматически
     */
    public function generate(): static
    {
        $list = new Collection();

        // Запускаем цикл по всем контекстам
        foreach (LinkableRegistry::all() as $linkableModel) {
            foreach ($linkableModel->allLinkable() as $linkable){
                /** @var Linkable $linkable */

                // Зарегистрируем модели в сервисе (если их нет)
                $linkable->relinking()->make();

                // Добавляем в едину коллекцию
                $list->add($linkable);
            }
        }

        foreach ($list->all() as $linkable) {
            $linkable->relinking()->generate();
        }

        return $this;
    }
}
