<?php

namespace Shared\MetaField;

use Nova\Database\ORM\Builder;


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
            return $this->saveOneMeta($meta, $value, true);
        }

        foreach ($meta as $key => $value) {
            $this->saveOneMeta($key, $value);
        }

        $this->load('meta');

        return true;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param boolean $load
     * @return bool
     */
    private function saveOneMeta($key, $value, $load = false)
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

        $result = $item->save();

        if ($load) {
            $this->load('meta');
        }

        return $result;
    }

    /**
     * @param string|array $meta
     * @param mixed $value
     * @return \Nova\Database\ORM\Model|\Nova\Support\Collection
     */
    public function createMeta($meta, $value = null)
    {
        if (! is_array($meta)) {
            return $this->createOneMeta($meta, $value, true);
        }

        $result = collect($meta)->map(function ($value, $key)
        {
            return $this->createOneMeta($key, $value);
        });

        $this->load('meta');

        return $result;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param boolean $load
     * @return \Nova\Database\ORM\Model
     */
    private function createOneMeta($key, $value, $load = false)
    {
        $item = $this->meta()->create(array(
            'key'   => $key,
            'value' => $value,
        ));

        if ($load) {
            $this->load('meta');
        }

        return $item;
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
