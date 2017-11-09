<?php

namespace App\Modules\Fields\Models;

use Nova\Database\ORM\Model;

use App\Modules\Fields\Models\Collection\FieldCollection;


class Field extends Model
{
    protected $table = 'fields';

    protected $primaryKey = 'id';

    protected $fillable = array('name', 'type', 'key', 'validate', 'order', 'columns', 'hidden');


    /**
     * Create a new ORM Collection instance.
     *
     * @param  array  $models
     * @return \App\Modules\Fields\Support\FieldCollection
     */
    public function newCollection(array $models = array())
    {
        return new FieldCollection($models);
    }
}
