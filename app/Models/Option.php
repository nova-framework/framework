<?php

namespace App\Models;

use Nova\Database\ORM\Model as BaseModel;


class Option extends BaseModel
{
    protected $table = 'options';

    protected $primaryKey = 'id';

    protected $fillable = array('namespace', 'group', 'item', 'value');

    public $timestamps = false;


    public function getValueAttribute($value)
    {
        return $this->maybeDecode($value);
    }

    public function setValueAttribute($value) {
        $this->attributes['value'] = $this->maybeEncode($value);
    }

    public static function set($key, $value)
    {
        list($namespace, $group, $item) = static::parseKey($key);

        // Prepare the record variables.
        $attributes = array(
            'namespace' => $namespace,
            'group'     => $group,
            'item'      => $item
        );

        $values = array(
            'value' => $value
        );

        return static::updateOrCreate($attributes, $values);
    }

    /**
     * Parse a key into namespace, group, and item.
     *
     * @param  string  $key
     * @return array
     */
    protected static function parseKey($key)
    {
        if (strpos($key, '::') === false) {
            $segments = explode('.', $key);

            return static::parseBasicSegments($segments);
        }

        $parsed = static::parseNamespacedSegments($key);
    }

    /**
     * Parse an array of basic segments.
     *
     * @param  array  $segments
     * @return array
     */
    protected static function parseBasicSegments(array $segments)
    {
        $group = $segments[0];

        if (count($segments) == 1) {
            return array(null, $group, null);
        }

        $item = implode('.', array_slice($segments, 1));

        return array(null, $group, $item);
    }

    /**
     * Parse an array of namespaced segments.
     *
     * @param  string  $key
     * @return array
     */
    protected static function parseNamespacedSegments($key)
    {
        list($namespace, $item) = explode('::', $key);

        $itemSegments = explode('.', $item);

        $groupAndItem = array_slice(static::parseBasicSegments($itemSegments), 1);

        return array_merge(array($namespace), $groupAndItem);
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
}
