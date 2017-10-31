<?php

namespace App\Modules\Platform\Traits;


trait HasActivitiesTrait
{
    public function activities()
    {
        return $this->hasMany('App\Modules\Platform\Models\Activity', 'user_id', 'id');
    }

    public function scopeActiveSince($query, $since)
    {
        return $query->with(array('activities' => function ($query)
        {
            return $query->orderBy('last_activity', 'DESC');

        }))->whereHas('activities', function ($query) use ($since)
        {
            return $query->where('last_activity', '>=', $since);
        });
    }
}
