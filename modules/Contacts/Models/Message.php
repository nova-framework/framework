<?php

namespace Modules\Contacts\Models;

use Nova\Database\ORM\Model as BaseModel;


class Message extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'contact_messages';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = array('contact_id', 'remote_ip', 'path', 'user_id');

    /**
     * @var array
     */
    protected $with = array('fields', 'attachments');


    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo('Modules\Contacts\Models\Contact', 'contact_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function fields()
    {
        return $this->hasMany('Modules\Contacts\Models\CustomField', 'message_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function attachments()
    {
        return $this->hasMany('Modules\Contacts\Models\Attachment', 'message_id');
    }

    /**
     * Listen to ORM events.
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function (Message $model)
        {
            $model->load('fields', 'attachments');

            $model->fields->each(function ($field)
            {
                $field->delete();
            });

            $model->attachments->each(function ($attachment)
            {
                $attachment->delete();
            });
        });
    }
}
