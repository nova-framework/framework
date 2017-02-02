<?php

namespace Messages\Traits;

use Messages\Models\Message;


trait HasMessagesTrait
{
    /**
     * Relationship between Message and User.
     */

    public function messages()
    {
        return $this->hasMany('Messages\Models\Message', 'sender_id');
    }

}
