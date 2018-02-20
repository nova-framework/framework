<?php

namespace Modules\Users\Models;

use Shared\Database\ORM\MetaField\MetaField as BaseModel;


class UserMeta extends BaseModel
{
    protected $table = 'users_meta';

    protected $primaryKey = 'id';

    protected $fillable = array('key', 'value', 'user_id');


    public function user()
    {
        return $this->belongsTo('Modules\Users\Models\User', 'user_id');
    }
}
