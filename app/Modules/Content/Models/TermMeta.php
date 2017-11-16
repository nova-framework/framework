<?php

namespace App\Modules\Content\Models;

use App\Modules\Content\Models\PostMeta;


class TermMeta extends PostMeta
{
    /**
     * @var string
     */
    protected $table = 'terms_meta';

    /**
     * @var array
     */
    protected $fillable = array('key', 'meta_value', 'term_id');


    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo('App\Modules\Content\Models\Term', 'term_id');
    }
}
