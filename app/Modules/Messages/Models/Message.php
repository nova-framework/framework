<?php

namespace App\Modules\Messages\Models;

use Nova\Database\ORM\Model as BaseModel;

use App\Modules\Users\Models\User;


class Message extends BaseModel
{
    protected $table = 'messages';

    protected $primaryKey = 'id';

    protected $fillable = array('subject', 'body', 'is_read');

    protected $touches = array('parent');


    public function sender()
    {
        return $this->belongsTo('App\Modules\Users\Models\User', 'sender_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo('App\Modules\Users\Models\User', 'receiver_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Modules\Messages\Models\Message', 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany('App\Modules\Messages\Models\Message', 'parent_id');
    }

    public function scopeNotReply($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', '=', 0);
    }

    // Set seen to 1 when user reads message.
    public function setReadBy(User $user)
    {
        if($this->is_read == 1) {
            return true;
        } else if($this->sender_id !== $user->id) {
            $this->update(array(
                'is_read' => 1
            ));

            return true;
        }

        return false;
    }
}
