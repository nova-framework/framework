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
    protected $fillable = array(
        'contact_id', 'author', 'author_email', 'author_ip', 'author_url', 'content', 'path', 'user_id'
    );

    /**
     * @var array
     */
    protected $with = array('attachments');


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
    public function attachments()
    {
        return $this->hasMany('Modules\Contacts\Models\Attachment', 'message_id');
    }
}
