<?php

namespace App\Modules\Demo\Models;

use Nova\ORM\Model as BaseModel;


class Post extends BaseModel
{
    protected $tableName = 'posts';

    protected $relations = array('author', 'category');


    public function __construct()
    {
        parent::__construct();
    }

    public function author()
    {
        return $this->belongsTo('App\Modules\Demo\Models\User', 'author_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Modules\Demo\Models\Category', 'category_id');
    }

}
