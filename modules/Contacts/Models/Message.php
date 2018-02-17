<?php

namespace Modules\Contacts\Models;

use Modules\Content\Models\Post;


class Message extends Post
{
    /**
     * @var string
     */
    protected $postType = 'contact_message';


    public function contact()
    {
        return $this->belongsTo('Modules\Contacts\Models\Contact', 'parent_id');
    }
}
