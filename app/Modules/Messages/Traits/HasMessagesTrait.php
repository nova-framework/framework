<?php

namespace App\Modules\Messages\Traits;


trait HasMessagesTrait
{
    public function messages()
    {
        return $this->hasMany('App\Modules\Messages\Models\Message', 'sender_id', 'id');
    }
}
