<?php

namespace Modules\Fields\Models;

use Nova\Database\ORM\Model;

use Modules\Fields\Models\Collection\FieldCollection;


class Field extends Model
{
    protected $table = 'fields';

    protected $primaryKey = 'id';

    protected $fillable = array('name', 'type', 'key', 'validate', 'order', 'columns', 'hidden');


    /**
     * Create a new ORM Collection instance.
     *
     * @param  array  $models
     * @return \Modules\Fields\Support\FieldCollection
     */
    public function newCollection(array $models = array())
    {
        return new FieldCollection($models);
    }
}
