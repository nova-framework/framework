<?php

namespace Modules\Content\Models;

use Shared\Database\ORM\MetaField\MetaField;


class TermMeta extends MetaField
{
    /**
     * @var string
     */
    protected $table = 'terms_meta';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = array('key', 'meta_value', 'term_id');


    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo('Modules\Content\Models\Term', 'term_id');
    }
}
