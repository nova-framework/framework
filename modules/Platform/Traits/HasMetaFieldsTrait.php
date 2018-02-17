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
     * @param string $meta
     * @param mixed $value
     * @return bool
     */
    public function saveMeta($key, $value = null)
    {
        if (! is_array($key)) {
            $result = $this->saveOneMeta($key, $value);
        }

        // Save multiple meta fields.
        else {
            foreach ($key as $innerKey => $innerValue) {
                $this->saveOneMeta($innerKey, $innerValue);
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
        $relation = $this->meta();

        $meta = $relation->where('key', $key)->firstOr(function () use ($relation, $key)
        {
            $related = $relation->getRelated();

            $foreignKey = $relation->getPlainForeignKey();

            return $related->newInstance(array(
                $foreignKey => $relation->getParentKey(),
                'key'       => $key,
            ));
        });

        $meta->value = $value;

        return $meta->save();
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return \Nova\Database\ORM\Model|\Nova\Support\Collection
     */
    public function createMeta($key, $value = null)
    {
        if (! is_array($key)) {
            $result = $this->createOneMeta($key, $value);
        }

        // Create and return a collection of meta fields.
        else {
            $result = collect($key)->map(function ($value, $key)
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
