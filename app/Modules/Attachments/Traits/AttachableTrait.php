<?php

namespace App\Modules\Attachments\Traits;


trait AttachableTrait
{

    /**
     * Get the entity's attachments.
     */
    public function attachments()
    {
        return $this->morphMany('App\Modules\Attachments\Models\Attachment', 'attachable');
    }
}
