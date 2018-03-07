<?php

namespace Modules\Users\Models;

use Shared\MetaField\Models\MetaField as BaseModel;


class UserMeta extends BaseModel
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
