<?php

namespace Modules\Content\Models;

use Shared\MetaField\Models\MetaField as BaseModel;


class TermMeta extends BaseModel
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
    protected $fillable = array('key', 'value', 'term_id');


    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo('Modules\Content\Models\Term', 'term_id');
    }
}
