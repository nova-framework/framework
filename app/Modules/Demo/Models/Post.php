<?php

namespace App\Modules\Demo\Models;

use Nova\ORM\Model as BaseModel;


class Post extends BaseModel
{
    protected $tableName = 'posts';

    protected $relations = array('user', 'category');


    public function __construct()
    {
        parent::__construct();
    }

    public function author()
    {
        return $this->belongsTo('App\Modules\Demo\User', 'author_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Modules\Demo\Category', 'category_id');
    }

}
