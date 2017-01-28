<?php

namespace Modules\Messages\Traits;

use Modules\Messages\Models\Message;


trait HasMessagesTrait
{
    /**
     * Relationship between Message and User.
     */

    public function messages()
    {
        return $this->hasMany('Modules\Messages\Models\Message', 'sender_id');
    }

}
