<?php

namespace Modules\Attachments\Traits;


trait AttachableTrait
{

    /**
     * Get the entity's attachments.
     */
    public function attachments()
    {
        return $this->morphMany('Modules\Attachments\Models\Attachment', 'attachable');
    }
}
