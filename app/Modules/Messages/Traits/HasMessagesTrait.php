<?php

namespace App\Modules\Messages\Traits;

use App\Modules\Messages\Models\Message;


trait HasMessagesTrait
{
    /**
     * Relationship between Message and User.
     */

    public function messages()
    {
        return $this->hasMany('App\Modules\Messages\Models\Message', 'sender_id');
    }

}
