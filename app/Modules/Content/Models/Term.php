<?php

namespace App\Modules\Content\Models;

use Nova\Database\ORM\Model;

use App\Modules\Content\Traits\HasMetaTrait;


class Term extends Model
{
    use HasMetaTrait;

    //
    protected $table = 'terms';

    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $with = array('meta');

    /**
     * @var bool
     */
    public $timestamps = false;


    /**
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany('App\Modules\Content\Models\TermMeta', 'term_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\HasOne
     */
    public function taxonomy()
    {
        return $this->hasOne('App\Modules\Content\Models\Taxonomy', 'term_id');
    }
}
