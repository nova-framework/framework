<?php

namespace Backend\Models;

use Nova\Database\ORM\Model as BaseModel;

use Nova\Support\Facades\Auth;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\Session;

use Carbon\Carbon;


class Activity extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'activities';

    protected $primaryKey = 'id';

    protected $fillable = array('session', 'user_id', 'ip', 'last_activity');

    public $timestamps = false;


    /**
     * Returns the user that belongs to this entry.
     */
    public function user()
    {
        return $this->belongsTo('Backend\Models\User', 'user_id', 'id');
    }

    /**
     * Updates the session of the current user.
     *
     * @param  $query
     * @return \Backend\Models\Activity
     */
    public static function updateCurrent($request)
    {
        if (! Auth::check()) {
            // We track only the authenticated users.
            return;
        }

        $attributes = array(
            'session' => Session::getId(),
            'user_id' => Auth::id(),
        );

        $model = static::updateOrCreate($attributes, array(
            'last_activity'    => strtotime(Carbon::now()),
            'ip'            => $request->ip()
        ));
    }
}
