<?php

namespace Modules\Users\Models;

use Shared\Database\ORM\MetaField\MetaField;


class UserMeta extends MetaField
{
    /**
     * @var string
     */
    protected $table = 'users_meta';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = array('key', 'value', 'user_id');


    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Modules\Users\Models\User', 'user_id');
    }
}
