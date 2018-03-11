<?php

namespace Modules\Contacts\Models;

use Nova\Database\ORM\Model as BaseModel;


class FieldGroup extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'contact_field_groups';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = array('title', 'order', 'hide_title');

    /**
     * @var array
     */
    protected $with = array('fieldItems');


    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo('Modules\Contacts\Models\Contact', 'contact_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function fieldItems()
    {
        return $this->hasMany('Modules\Contacts\Models\FieldItem', 'field_group_id');
    }
}
