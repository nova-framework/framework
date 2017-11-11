<?php

namespace App\Modules\Content\Models;

use App\Modules\Content\Models\Post;


class CustomLink extends Post
{
    //
    protected $table = 'posts';

    protected $primaryKey = 'id';


    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($name === 'url') {
            return $this->meta->menu_item_url;
        }

        if ($name === 'link_text') {
            return $this->title;
        }

        return parent::__get($name);
    }
}
