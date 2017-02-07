<?php

namespace Modules\System\Models;

use Modules\System\Database\Model as BaseModel;


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

    public static function set($group, $item, $value)
    {
        // Prepare the variables.
        $attributes = array(
            'group' => $group,
            'item'  => $item
        );

        $values = array(
            'value' => $value
        );

        return static::updateOrCreate($attributes, $values);
    }

}
