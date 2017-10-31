<?php

namespace App\Modules\Attachments\Traits;


trait HasAttachmentsTrait
{

    /**
     * Get the entity's attachments.
     */
    public function attachments()
    {
        return $this->hasMany('App\Modules\Attachments\Models\Attachment', 'user_id', 'id');
    }
}
