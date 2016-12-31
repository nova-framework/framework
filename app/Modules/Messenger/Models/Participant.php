<?php

namespace App\Modules\Messenger\Models;

use Nova\Database\ORM\Model;
use Nova\Database\ORM\SoftDeletingTrait;
use Nova\Support\Facades\Config;


class Participant extends Model
{
    use SoftDeletingTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'participants';

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = array('thread_id', 'user_id', 'last_read');

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = array('created_at', 'updated_at', 'deleted_at', 'last_read');

    /**
     * Thread relationship
     *
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo('App\Modules\Messenger\Models\Thread');
    }

    /**
     * User relationship
     *
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Modules\System\Models\User');
    }

}
