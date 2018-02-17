<?php

namespace Modules\Users\Models;

use Nova\Database\ORM\Model;

use Modules\Fields\Support\MetaCollection;


class Profile extends Model
{
    protected $table = 'user_profiles';

    protected $primaryKey = 'id';

    protected $with = array('fields');


    public function fields()
    {
        return $this->morphMany('Modules\Fields\Models\Field', 'model');
    }

    public function users()
    {
        return $this->hasMany('Modules\Users\Models\User', 'profile_id');
    }

    public function getMetaFields(MetaCollection $meta)
    {
        return $this->fields->getMetaFields($meta);
    }
}
