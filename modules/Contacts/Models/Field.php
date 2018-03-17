<?php

namespace Modules\Contacts\Models;

use Nova\Database\ORM\Model as BaseModel;

use Modules\Contacts\Models\FieldCollection;


class Field extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'contact_fields';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = array('message_id', 'field_item_id', 'type', 'name', 'value');


    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function message()
    {
        return $this->belongsTo('Modules\Contacts\Models\Message', 'message_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function fieldItem()
    {
        return $this->belongsTo('Modules\Contacts\Models\FieldItem', 'field_item_id');
    }

    /**
     * @param array $models
     * @return MetaCollection
     */
    public function newCollection(array $models = array())
    {
        return new FieldCollection($models);
    }

    /**
     * @param  mixed  $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        try {
            $result = @unserialize($value);

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
     * @return string
     */
    public function getValueString()
    {
        $type = $this->getAttribute('type');

        $value = $this->getAttribute('value');

        if (($type == 'text') || ($type == 'textarea')) {
            return $value;
        }

        // Only the types with choices will be processed further.
        else if (($type != 'select') && ($type != 'checkbox') && ($type != 'radio')) {
            return;
        }

        $options = (array) $this->fieldItem->getAttribute('options');

        $values = is_array($value) ? $value : array($value);

        $labels = array_filter(array_map(function ($choice) use ($values)
        {
            if (! Str::contains($choice, ':')) {
                return;
            }

            list ($value, $label) = array_map('trim', explode(':', $choice));

            if (in_array($value, $values)) {
                return $label;
            }
        },
        explode("\n", Arr::get($options, 'choices'))), function ($label)
        {
            return ! empty($label);
        });

        if (! empty($labels)) {
            return implode(', ', $labels);
        }
    }
}
