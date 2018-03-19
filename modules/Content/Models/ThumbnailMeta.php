<?php

namespace Modules\Content\Models;

use Nova\Support\Arr;

use Modules\Content\Models\PostMeta;


class ThumbnailMeta extends PostMeta
{
    const SIZE_THUMBNAIL = 'thumbnail';
    const SIZE_MEDIUM    = 'medium';
    const SIZE_LARGE     = 'large';
    const SIZE_FULL      = 'full';

    /**
     * @var array
     */
    protected $with = array('attachment');


    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function attachment()
    {
        return $this->belongsTo('Modules\Content\Models\Attachment', 'value');
    }

    /**
     * @param string $size
     * @return array
     * @throws \Exception
     */
    public function size($size)
    {
        if ($size == self::SIZE_FULL) {
            return $this->attachment->url;
        }

        $meta = unserialize($this->attachment->meta->attachment_metadata);

        $sizes = Arr::get($meta, 'sizes');

        if (! isset($sizes[$size])) {
            return $this->attachment->url;
        }

        $data = Arr::get($sizes, $size);

        return array_merge($data, array(
            'url' => dirname($this->attachment->url) .'/' .$data['file'],
        ));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->attachment->guid;
    }
}
