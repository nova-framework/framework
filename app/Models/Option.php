<?php

namespace App\Models;

use Nova\Database\ORM\Model as BaseModel;


class Option extends BaseModel
{
    protected $table = 'options';

    protected $primaryKey = 'id';

    protected $fillable = array('group', 'item', 'value');

    public $timestamps = false;


    public static function set($key, $value)
    {
        list($group, $item) = static::parseKey($key);

        //
        $attributes = array('group' => $group, 'item'  => $item);

        $values = array('value' => $value);

        return static::updateOrCreate($attributes, $values);
    }

    public function getValueAttribute($value)
    {
        return $this->maybeDecode($value);
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = $this->maybeEncode($value);
    }

    /**
     * Decode value only if it was encoded to JSON.
     *
     * @param  string  $original
     * @param  bool    $assoc
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

    /**
     * Parse a key into namespace, group, and item.
     *
     * @param  string  $key
     * @return array
     */
    protected static function parseKey($key)
    {
        $segments = explode('.', $key);

        $group = head($segments);

        if (count($segments) == 1) {
            return array($group, null);
        }

        $item = implode('.', array_slice($segments, 1));

        return array($group, $item);
    }
}
