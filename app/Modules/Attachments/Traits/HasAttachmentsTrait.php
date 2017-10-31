<?php

namespace App\Modules\Attachments\Traits;


trait HasAttachmentsTrait
{

    /**
     * Get the entity's attachments.
     */
    public function attachments()
    {
        return $this->morphMany('App\Modules\Attachments\Models\Attachment', 'ownerable');
    }
}
