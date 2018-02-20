<?php

namespace Shared\Database\ORM\MetaField;

use Nova\Database\ORM\Model as BaseModel;

use Shared\Database\ORM\MetaField\Collection;

use Exception;


abstract class MetaField extends BaseModel
{
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    public $timestamps = false;


    /**
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        try {
            $result = unserialize($value);

            if (($result === false) && ($value !== false)) {
                return $value;
            }

            return $result;
        }
        catch (Exception $e) {
            return $value;
        }
    }

    /**
     * @param  mixed  $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        if (is_array($value) || is_object($value)) {
            $value = serialize($value);
        }

        // When the value is a string containing serialized data, we should serialize it again.
        else if (is_string($value) && preg_match("#^((N;)|((a|O|s):[0-9]+:.*[;}])|((b|i|d):[0-9.E-]+;))$#um", $value)) {
            $value = serialize($value);
        }

        $this->attributes['value'] = $value;
    }

    /**
     * @param array $models
     * @return MetaCollection
     */
    public function newCollection(array $models = array())
    {
        return new Collection($models);
    }
}
