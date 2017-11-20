<?php

namespace App\Modules\Contacts\Models;

use App\Modules\Content\Models\Post;


class Message extends Post
{
    /**
     * @var string
     */
    protected $postType = 'contact_message';


    public function contact()
    {
        return $this->belongsTo('App\Modules\Contacts\Models\Contact', 'parent_id');
    }
}
