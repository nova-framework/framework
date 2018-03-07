<?php

namespace Modules\Contacts\Models;

use Modules\Content\Models\Post;


class Message extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'contacts';

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


    public function contact()
    {
        return $this->belongsTo('Modules\Contacts\Models\Contact', 'contact_id');
    }
}
