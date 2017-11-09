<?php

namespace App\Modules\Users\Models;

use Nova\Database\ORM\Model;

use App\Modules\Fields\Support\MetaCollection;


class Profile extends Model
{
    protected $table = 'profiles';

    protected $primaryKey = 'id';

    protected $with = array('fields');


    public function fields()
    {
        return $this->morphMany('App\Modules\Fields\Models\Field', 'model');
    }

    public function users()
    {
        return $this->hasMany('App\Modules\Users\Models\User', 'profile_id');
    }

    public function getMetaFields(MetaCollection $meta)
    {
        return $this->fields->getMetaFields($meta);
    }
}
