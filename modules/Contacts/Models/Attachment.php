<?php

namespace Modules\Contacts\Models;

use Nova\Database\ORM\Model as BaseModel;
use Nova\Support\Facades\File;
use Nova\Support\Facades\Log;
use Nova\Support\Str;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Exception;


class Attachment extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'contact_attachments';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = array(
        'parent_id', 'name', 'size', 'type', 'path'
    );

    /**
     * Where we store the uploaded files.
     *
     * @var string
     */
    protected static $path = STORAGE_PATH .'files' .DS .'contacts';


    public function parent()
    {
        return $this->belongsTo('Modules\Contacts\Models\Message', 'parent_id');
    }

    /**
     * Listen to ORM events.
     *
     * Cleanup properly on update and delete
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function (Model $model)
        {
            // Nothing to delete when the original path is empty.
            if (empty($path = $model->getOriginal('path'))) {
                return;
            }

            // We will delete the previous file on path changes.
            else if ($path != $model->getAttributeValue('path')) {
                static::deleteFile($path);
            }
        });

        static::deleting(function (Model $model)
        {
            // Don't delete the file if you are doing a soft delete!
            if (method_exists($model, 'restore') && ! $model->forceDeleting) {
                return;
            }

            // If there is a file path specified, we will delete it.
            else if (! empty($path = $model->getAttributeValue('path'))) {
                static::deleteFile($path);
            }
        });
    }

    /**
     * Delete the specified file with the errors logging.
     *
     * @param string $file
     * @return void
     */
    protected static function deleteFile($file)
    {
        try {
            File::delete($file);
        }
        catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Handle the UploadedFile and create a new record.
     *
     * @param UploadedFile $file
     * @return \Modules\Contacts\Models\Attachment|null
     */
    public static function uploadFileAndCreate(UploadedFile $file)
    {
        if (! File::exists($path = static::$path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        $name = $file->getClientOriginalName();

        $fileName = pathinfo($name, PATHINFO_FILENAME);

        $extension = $file->guessClientExtension();

        // Compute the uploaded file path.
        $path = sprintf('%s/%s-%s.%s', $path, uniqid(), Str::slug($fileName), $extension);

        if (! File::put($path, fopen($file->getRealPath(), 'r+'))) {
            return null;
        }

        return static::create(array(
            'name' => $name,
            'size' => $file->getSize(),
            'type' => $file->getClientMimeType(),
            'path' => $path,

            // Will be updated later, when it will be attached to parent.
            'parent_id' => 0,
        ));
    }
}
