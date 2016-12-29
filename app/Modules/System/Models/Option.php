<?php

namespace App\Modules\System\Models;

use Nova\Database\ORM\Model as BaseModel;


class Option extends BaseModel
{
    protected $table = 'options';

    protected $primaryKey = 'id';

    protected $fillable = array('group', 'item', 'value');

    public $timestamps = false;


    public function getValueAttribute($value) {
        return $this->maybeDecode($value);
    }

    public function setValueAttribute($value) {
        $this->attributes['value'] = $this->maybeEncode($value);
    }

    public static function set($key, $value)
    {
        // Prepare the variables.
        $attributes = array(
            'group' => $group,
            'item'  => $item
        );

        $values = array(
            'value' => $this->maybeEncode($value)
        );

        return static::updateOrCreate($attributes, $values);
    }

    /**
     * Parse a key into group, and item.
     *
     * @param  string  $key
     * @return array
     */
    private static function parseKey($key)
    {
        $segments = explode('.', $key);

        $group = array_shift($segments);

        if (! empty($segments)) {
            $segments = implode('.', $segments);
        } else {
            $segments = null;
        }

        return array($group, $segments);
    }

    /**
     * Decode value only if it was encoded to JSON.
     *
     * @param  string  $original
     * @param  bool    $assoc
     * @return mixed
     */
    private function maybeDecode($original, $assoc = true)
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
    private function maybeEncode($data)
    {
        if (is_array($data) || is_object($data)) {
            return json_encode($data);
        }

        return $data;
    }
}
