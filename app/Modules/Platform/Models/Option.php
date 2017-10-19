<?php

namespace App\Modules\Platform\Models;

use Nova\Database\ORM\Model as BaseModel;
use Nova\Database\QueryException;
use Nova\Support\NamespacedItemResolver;

use PDOException;


class Option extends BaseModel
{
    protected $table = 'options';

    protected $primaryKey = 'id';

    protected $fillable = array('namespace', 'group', 'item', 'value');

    public $timestamps = false;

    //
    protected static $itemResolver;


    public function getValueAttribute($value)
    {
        return $this->maybeDecode($value);
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = $this->maybeEncode($value);
    }

    public function getConfigItem()
    {
        if (! empty($namespace = $this->getAttribute('namespace'))) {
            $key = $namespace .'::';
        } else {
            $key = '';
        }

        $key .= $this->getAttribute('group');

        if (! empty($item = $this->getAttribute('item'))) {
            $key .= '.' .$item;
        }

        return array($key, $this->getAttribute('value'));
    }

    public static function getResults()
    {
        $instance = new static;

        try {
            return $instance->newQuery()->get();
        }
        catch (QueryException $e) {
            //
        }
        catch (PDOException $e) {
            //
        }

        return $instance->newCollection();
    }

    public static function set($key, $value)
    {
        list($namespace, $group, $item) = static::getItemResolver()->parseKey($key);

        return static::updateOrCreate(
            compact('namespace', 'group', 'item'), compact('value')
        );
    }

    protected static function getItemResolver()
    {
        if (! isset(static::$itemResolver)) {
            return static::$itemResolver = new NamespacedItemResolver();
        }

        return static::$itemResolver;
    }

    /**
     * Decode value only if it was encoded to JSON.
     *
     * @param  string  $original
     * @param  bool  $assoc
     * @return mixed
     */
    protected function maybeDecode($original, $assoc = true)
    {
        if (is_numeric($original)) return $original;

        $data = json_decode($original, $assoc);

        return (is_object($data) || is_array($data)) ? $data : $original;
    }

    /**
     * Encode data to JSON, if needed.
     *
     * @param  mixed  $data
     * @return mixed
     */
    protected function maybeEncode($data)
    {
        if (is_array($data) || is_object($data)) {
            return json_encode($data);
        }

        return $data;
    }
}
