<?php

namespace AcmeCorp\Backend\Models;

use Nova\Database\ORM\Model as BaseModel;

use AcmeCorp\Backend\Models\User;


class Message extends BaseModel
{
    protected $table = 'messages';

    protected $primaryKey = 'id';

    protected $fillable = array('subject', 'body', 'seen', 'is_read');


    public function sender()
    {
        return $this->belongsTo('AcmeCorp\Backend\Models\User', 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo('AcmeCorp\Backend\Models\User', 'receiver_id');
    }

    public function scopeNotReply($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', '=', 0);
    }

    public function replies()
    {
        return $this->hasMany('AcmeCorp\Backend\Models\Message', 'parent_id');
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
