<?php

namespace Modules\Contacts\Models;

use Nova\Database\ORM\Model as BaseModel;


class FieldItem extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'contact_field_items';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = array('title', 'slug', 'type', 'order', 'rules', 'options', 'field_group_id');


    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function fieldGroup()
    {
        return $this->belongsTo('Modules\Contacts\Models\FieldGroup', 'field_group_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function fields()
    {
        return $this->hasMany('Modules\Contacts\Models\CustomField', 'field_item_id');
    }

    /**
     * Listen to ORM events.
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function (FieldItem $model)
        {
            $model->load('fields');

            $model->fields->each(function ($field)
            {
                $field->delete();
            });
        });
    }
}
