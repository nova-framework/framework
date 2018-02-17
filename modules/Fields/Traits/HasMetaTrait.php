<?php

namespace Modules\Fields\Traits;

use Nova\Database\ORM\Relations\HasMany;
use Nova\Database\ORM\Collection;
use Nova\Database\ORM\Builder;
use Nova\Support\Facades\Schema;
use Nova\Support\Collection as BaseCollection;
use Nova\Support\Str;

use Modules\Fields\Models\MetaData;

use InvalidArgumentException;


trait HasMetaTrait
{

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootHasMetaTrait()
    {
        static::observeSaveAndCascade();

        static::observeDeleteAndCascade();
    }

    public function meta()
    {
        $model = new MetaData();

        $model->setTable($this->getMetaTable());

        return new HasMany($model->newQuery(), $this, $this->getMetaKeyName(), $this->getKeyName());
    }

    /**
     * @param Builder $query
     * @param string $meta
     * @param mixed $value
     * @return Builder
     */
    public function scopeHasMeta(Builder $query, $meta, $value = null)
    {
        if (! is_array($meta)) {
            $meta = array($meta => $value);
        }

        foreach ($meta as $key => $value) {
            $query->whereHas('meta', function ($query) use ($key, $value)
            {
                if (is_string($key)) {
                    $query->where('key', $key);

                    return is_null($value)
                        ? $query                          // 'foo' => null
                        : $query->where('value', $value); // 'foo' => 'bar'
                }

                return $query->where('key', $value);      // 0 => 'foo'
            });
        }

        return $query;
    }

    /**
     * Determine if the model or given attribute(s) have been modified.
     *
     * @param  array|string|null  $attributes
     * @return bool
     */
    public function isDirty($attributes = null)
    {
        if (parent::isDirty($attributes)) {
            return true;
        } else if (! isset($this->meta)) {
            return false;
        }

        foreach ($this->meta as $item) {
            if ($item->isDirty($attributes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Observes the model and saves dirty meta data on save.
     *
     * @return void
     */
    public static function observeSaveAndCascade()
    {
        static::saved(function ($model)
        {
            if (! isset($model->meta)) {
                return;
            }

            //
            // Remove any keys not present in the collection.

            $keyName = with(new MetaData())->getKeyName();

            $ids = array_diff($model->meta->originalModelKeys(), $model->meta->modelKeys());

            if (! empty($ids)) {
                $model->meta()->whereIn($keyName, $ids)->delete();
            }

            //
            // Save dirty meta items.

            $metaTable = $model->getMetaTable();

            foreach ($model->meta as $item) {
                if (! $item->isDirty()) {
                    continue;
                }

                if (! $item->hasTable()) {
                    $item->setTable($metaTable);
                }

                if ($item->exists) {
                    $item->save();
                } else {
                    $model->meta()->save($item);
                }
            }
        });
    }

    /**
     * Observes the model and deletes meta entries on delete.
     *
     * @return void
     */
    public static function observeDeleteAndCascade()
    {
        static::deleted(function ($model)
        {
            $model->meta()->delete();
        });
    }

    /**
     * Return the foreign key name for the meta table.
     *
     * @return string
     */
    public function getMetaKeyName()
    {
        return isset($this->metaKeyName) ? $this->metaKeyName : $this->getForeignKey();
    }

    /**
     * Return the table name.
     *
     * @return null
     */
    public function getMetaTable()
    {
        if (isset($this->metaTable)) {
            return $this->metaTable;
        }

        return $this->getTable() .'_meta';
    }
}
