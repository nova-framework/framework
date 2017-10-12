<?php

namespace App\Modules\Chat\Models;

use Nova\Database\ORM\Model as BaseModel;

use App\Models\User;


class Message extends BaseModel
{
    protected $table = 'chat_messages';

    protected $primaryKey = 'id';

    protected $fillable = array('sender_id', 'target_id', 'type', 'content');


    public function sender()
    {
        return $this->belongsTo('App\Models\User', 'sender_id', 'id');
    }

    public function target()
    {
        return $this->belongsTo('App\Models\User', 'target_id', 'id');
    }
}
