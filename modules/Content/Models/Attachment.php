<?php

namespace Modules\Content\Models;

use Nova\Database\ORM\Model;
use Nova\Support\Facades\Config;
use Nova\Support\Facades\File;
use Nova\Support\Facades\Log;

use Modules\Content\Models\Post;

use Exception;


class Attachment extends Post
{
    /**
     * @var string
     */
    protected $postType = 'attachment';

    /**
     * @var array
     */
    protected $appends = array(
        'url',
        'type',
        'description',
        'caption',
        'alt',
    );

    /**
     * @var array
     */
    protected static $aliases = array(
        'url'         => 'guid',
        'type'        => 'mime_type',
        'description' => 'content',
        'caption'     => 'excerpt',
        'alt'         => array(
            'meta' => 'attachment_image_alt'
        ),
    );

    /**
     * Listen to ORM events.
     *
     * Cleanup properly on update and delete.
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function (Model $model)
        {
            if (! $model instanceof Attachment) {
                return;
            }

            if (! is_null($originalName = $model->getOriginal('name'))) {
                $name = $model->getAttributeValue('name');

                if ($originalName !== $name) {
                    $path = (isset($model->meta) && isset($model->meta->attachment_image_path))
                        ? dirname($model->meta->attachment_image_path)
                        : null;

                    static::deleteUploadedFile($name, $path);
                }
            }
        });

        static::deleting(function (Model $model)
        {
            if (! $model instanceof Attachment) {
                return;
            }

            // Don't delete the file if you are doing a soft delete!
            if (! method_exists($model, 'restore') || $model->forceDeleting) {
                $name = $model->getAttribute('name');

                $path = (isset($model->meta) && isset($model->meta->attachment_image_path))
                    ? dirname($model->meta->attachment_image_path)
                    : null;

                static::deleteUploadedFile($name, $path);
            }
        });
    }

    protected static function deleteUploadedFile($name, $path)
    {
        if (is_null($path)) {
            $path = Config::get('content::attachments.path', base_path('assets/files'));
        }

        $fileName = pathinfo($name, PATHINFO_FILENAME);

        $extension = pathinfo($name, PATHINFO_EXTENSION);

        try {
            // Delete the uploaded file.
            File::delete($path .DS .$name);

            // Delete also the generated thumbnails, if any exists.
            $thumbnails = File::glob($path .DS .'thumbnails' .DS .$fileName .'-*.' .$extension);

            File::delete($thumbnails);
        }
        catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
