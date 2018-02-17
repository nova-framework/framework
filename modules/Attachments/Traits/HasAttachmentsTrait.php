<?php

namespace Modules\Attachments\Traits;


trait HasAttachmentsTrait
{

    /**
     * Get the entity's attachments.
     */
    public function attachments()
    {
        return $this->morphMany('Modules\Attachments\Models\Attachment', 'ownerable');
    }
}
