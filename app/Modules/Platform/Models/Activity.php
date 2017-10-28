<?php

namespace App\Modules\Platform\Models;

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
        return $this->belongsTo('App\Modules\Users\Models\User', 'user_id', 'id');
    }

    /**
     * Updates the session of the current user.
     *
     * @param  $query
     * @return \App\Modules\Platform\Models\Activity
     */
    public static function updateCurrent($request)
    {
        $guard = Auth::guard();

        if (! $guard->check()) {
            // We track only the authenticated users.
            return;
        }

        $now = Carbon::now();

        $attributes = array(
            'session' => Session::getId(),
            'user_id' => $guard->id(),
        );

        $model = static::updateOrCreate($attributes, array(
            'last_activity' => $now->timestamp,
            'ip'            => $request->ip(),
        ));
    }
}
