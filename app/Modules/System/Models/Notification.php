<?php

namespace App\Modules\System\Models;

use Nova\Database\ORM\Model;
use Nova\Database\ORM\ModelNotFoundException;

use Carbon\Carbon;


class Notification extends Model
{
    protected $table = 'notifications';

    protected $primaryKey = 'id';

    protected $fillable = array('sender_id', 'user_id', 'type', 'subject', 'body', 'object_id', 'object_type');

    private $relatedObject = null;


    public function user()
    {
        return $this->belongsTo('App\Modules\Users\Models\User', 'user_id');
    }

    public function sender()
    {
        return $this->belongsTo('App\Modules\Users\Models\User', 'sender_id');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', '=', 0);
    }

    public function withSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    public function withBody($body)
    {
        $this->body = $body;

        return $this;
    }

    public function withType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function regarding($object)
    {
        if (is_object($object)) {
            $this->object_id = $object->id;

            $this->object_type = get_class($object);
        }

        return $this;
    }

    public function from($user)
    {
        $this->sender()->associate($user);

        return $this;
    }

    public function to($user)
    {
        $this->user()->associate($user);

        return $this;
    }

    public function deliver()
    {
        $this->save();

        return $this;
    }

    public function hasValidObject()
    {
        try {
            $object = call_user_func(array($this->object_type, 'findOrFail'), $this->object_id);
        }
        catch (ModelNotFoundException $e) {
            return false;
        }

        $this->relatedObject = $object;

        return true;
    }

    public function getObject()
    {
        if(! isset($this->relatedObject)) {
            $hasObject = $this->hasValidObject();
        } else {
            $hasObject = true;
        }

        return $hasObject ? $this->relatedObject : false;
    }

    public function markAsRead()
    {
        $this->is_read = 1;

        $this->save();
    }
}
