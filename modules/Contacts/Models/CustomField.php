<?php

namespace Modules\Contacts\Models;

use Nova\Database\ORM\Model as BaseModel;


class CustomField extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'contact_custom_fields';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = array('message_id', 'field_item_id', 'type', 'slug', 'value');


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
}
