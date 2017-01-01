<?php

namespace App\Modules\System\Models;

use App\Modules\System\Database\Model;


class Log extends Model
{
    protected $table = 'logs';

    protected $primaryKey = 'id';

    protected $fillable = array('user_id', 'group_id', 'url', 'message', 'data');


    public function group()
    {
        return $this->belongsTo('App\Modules\System\Models\LogGroup', 'group_id');
    }

    public function users()
    {
        return $this->hasOne('App\Modules\Users\User', 'id', 'user_id');
    }

    public function getDataAttribute($value) {
        return $this->maybeDecode($value);
    }

    public function setDataAttribute($value) {
        $this->attributes['data'] = $this->maybeEncode($value);
    }

}
