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
    protected $fillable = array('title', 'slug', 'type', 'order', 'options');


    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function fieldGroup()
    {
        return $this->belongsTo('Modules\Contacts\Models\FieldGroup', 'field_group_id');
    }
}
