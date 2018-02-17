<?php

namespace Modules\Platform\Traits;

use Nova\Support\Arr;


trait AliasesTrait
{

    /**
     * @return array
     */
    public static function getAliases()
    {
        if (isset(parent::$aliases) && (count(parent::$aliases) > 0)) {
            return array_merge(parent::$aliases, static::$aliases);
        }

        return static::$aliases;
    }

    /**
     * @param string $new
     * @param string $old
     */
    public static function addAlias($new, $old)
    {
        static::$aliases[$new] = $old;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (is_null($value)) {
            $aliases = static::getAliases();

            if ((count($aliases) > 0) && ! is_null($value = Arr::get($aliases, $key))) {
                if (is_array($value)) {
                    $meta = Arr::get($value, 'meta');

                    return ! is_null($meta) ? $this->meta->getItem($meta) : null;
                }

                return parent::getAttribute($value);
            }
        }

        return $value;
    }

    /**
     * Get alias value from mutator or directly from attribute
     *
     * @param  string $key
     * @param  mixed $value
     * @return mixed
     */
    public function mutateAttribute($key, $value)
    {
        if ($this->hasGetMutator($key)) {
            return parent::mutateAttribute($key, $value);
        }

        return $this->getAttribute($key);
    }
}
