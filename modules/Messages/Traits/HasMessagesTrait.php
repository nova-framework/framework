<?php

namespace Modules\Messages\Traits;


trait HasMessagesTrait
{
    public function messages()
    {
        return $this->hasMany('Modules\Messages\Models\Message', 'sender_id', 'id');
    }
}
