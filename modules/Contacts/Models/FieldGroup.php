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
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function fieldItems()
    {
        return $this->hasMany('Modules\Contacts\Models\FieldItem', 'field_group_id');
    }
}
