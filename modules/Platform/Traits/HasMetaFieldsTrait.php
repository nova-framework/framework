<?php

namespace Modules\Platform\Traits;

use Nova\Database\ORM\Builder;
use Nova\Database\ORM\ModelNotFoundException;


trait HasMetaFieldsTrait
{

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootHasMetaFieldsTrait()
    {
        static::deleted(function ($model)
        {
            $model->meta()->delete();
        });
    }

    /**
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    abstract public function meta();

    /**
     * @param \Nova\Database\ORM\Builder $query
     * @param string $meta
     * @param mixed $value
     * @return \Nova\Database\ORM\Builder
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
     * @param string|array $meta
     * @param mixed $value
     * @return bool
     */
    public function saveMeta($meta, $value = null)
    {
        if (! is_array($meta)) {
            $result = $this->saveOneMeta($meta, $value);
        }

        // Save multiple meta fields.
        else {
            foreach ($meta as $key => $value) {
                $this->saveOneMeta($key, $value);
            }

            $result = true;
        }

        $this->load('meta');

        return $result;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    private function saveOneMeta($key, $value)
    {
        $meta = $this->meta();

        $item = $meta->where('key', $key)->firstOr(function () use ($meta, $key)
        {
            $foreignKey = $meta->getPlainForeignKey();

            $attributes = array(
                $foreignKey => $meta->getParentKey(),
                'key'       => $key,
            );

            return $meta->getRelated()->newInstance($attributes);
        });

        $item->value = $value;

        return $item->save();
    }

    /**
     * @param string|array $meta
     * @param mixed $value
     * @return \Nova\Database\ORM\Model|\Nova\Support\Collection
     */
    public function createMeta($meta, $value = null)
    {
        if (! is_array($meta)) {
            $result = $this->createOneMeta($meta, $value);
        }

        // Create and return a collection of meta fields.
        else {
            $result = collect($meta)->map(function ($value, $key)
            {
                return $this->createOneMeta($key, $value);
            });
        }

        $this->load('meta');

        return $result;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return \Nova\Database\ORM\Model
     */
    private function createOneMeta($key, $value)
    {
        return $this->meta()->create(array(
            'key'   => $key,
            'value' => $value,
        ));
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getMeta($name)
    {
        if (! is_null($key = $this->meta->findItem($name))) {
            $item = $this->meta->get($key);

            return $item->value;
        }
    }
}
