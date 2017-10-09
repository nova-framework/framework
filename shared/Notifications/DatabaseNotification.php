<?php

namespace Shared\Notifications;

use Nova\Database\ORM\Model as BaseModel;
use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Contracts\ArrayableInterface;

use Shared\Notifications\DatabaseNotificationCollection as Collection;


class DatabaseNotification extends BaseModel
{
    /**
     * The table associated with the Model.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * The primary key of the Model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The fillable attributes on the Model.
     *
     * @var array
     */
    protected $fillable = array(
        'uuid', 'type', 'notifiable_id', 'notifiable_type', 'data', 'read_at'
    );

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = array('read_at');


    /**
     * Get the notifiable entity that the notification belongs to.
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Get the data attribute.
     */
    public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Set the data attribute.
     */
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = json_encode($value);
    }

    /**
     * Mark the notification as read.
     *
     * @return void
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(array(
                'read_at' => $this->freshTimestamp()
            ));

            $this->save();
        }
    }

    /**
     * Determine if a notification has been read.
     *
     * @return bool
     */
    public function read()
    {
        return $this->read_at !== null;
    }

    /**
     * Determine if a notification has not been read.
     *
     * @return bool
     */
    public function unread()
    {
        return $this->read_at === null;
    }

    /**
     * Create a new database notification collection instance.
     *
     * @param  array  $models
     * @return \Backend\Notifications\Collection
     */
    public function newCollection(array $models = array())
    {
        return new Collection($models);
    }
}
