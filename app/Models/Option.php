<?php

namespace App\Models;

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

    public function getConfigKey()
    {
        if (! empty($namespace = $this->getAttribute('namespace'))) {
            $namespace .= '::';
        }

        $key = $namespace .$this->getAttribute('group');

        if (! empty($item = $this->getAttribute('item'))) {
            return $key .'.' .$item;
        }

        return $key;
    }

    public static function all($columns = array('*'))
    {
        try {
            return parent::all($columns);
        }
        catch (PDOException | QueryException $e) {
            //
        }

        return with(new static())->newCollection();
    }

    public static function set($key, $value)
    {
        list ($namespace, $group, $item) = static::getItemResolver()->parseKey($key);

        return static::updateOrCreate(
            compact('namespace', 'group', 'item'), compact('value')
        );
    }

    protected static function getItemResolver()
    {
        if (isset(static::$itemResolver)) {
            return static::$itemResolver;
        }

        return static::$itemResolver = new NamespacedItemResolver();
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
        if (is_numeric($original)) {
            return $original;
        }

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
