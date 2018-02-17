<?php

namespace Modules\Content\Traits;

use Nova\Database\ORM\Builder;
use Nova\Support\Str;

use Modules\Content\Models\PostMeta;

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

            $keyName = with(new PostMeta())->getKeyName();

            $ids = array_diff($model->meta->originalModelKeys(), $model->meta->modelKeys());

            if (! empty($ids)) {
                $model->meta()->whereIn($keyName, $ids)->delete();
            }

            //
            // Save dirty meta items.

            foreach ($model->meta as $item) {
                if (! $item->isDirty()) {
                    continue;
                }

                // The meta item should be saved.
                else if ($item->exists) {
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
}
