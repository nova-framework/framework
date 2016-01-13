<?php

namespace App\Modules\Demo\Models;

use Nova\ORM\Model as BaseModel;


class Category extends BaseModel
{
    protected $tableName = 'categories';

    protected $relations = array('posts');


    public function __construct()
    {
        parent::__construct();
    }

    public function posts()
    {
        return $this->hasMany('App\Modules\Demo\Post', 'category_id');
    }

}
