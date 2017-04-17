<?php

namespace Shared\Database\ORM\FileField;

use Nova\Database\ORM\Model;
use Nova\Support\Facades\File;
use Nova\Support\Facades\Log;
use Nova\Support\Str;

use Shared\Database\ORM\FileField\FileField;

use Symfony\Component\HttpFoundation\File\UploadedFile;


trait FileFieldTrait
{
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
            $files = (array) $model->files;

            if (count($files) > 0) {
                foreach ($files as $key => $options) {
                    $filePath = $model->getOriginal($key);

                    if (is_null($filePath)) continue;

                    $fileField = $model->getAttributeValue($key);

                    if ($fileField->path == $filePath) {
                        continue;
                    }

                    try {
                        File::delete($filePath);
                    }

                    // Catch all exceptions.
                    catch (Exception $e) {
                        Log::error($e->getMessage());
                    }
                }
            }
        });

        static::deleting(function (Model $model)
        {
            // Don't delete the file if you are doing a soft delete!
            if (! method_exists($model, 'restore') || $model->forceDeleting) {
                $files = (array) $model->files;

                if (count($files) > 0) {
                    foreach ($files as $key => $options) {
                        $file = $model->getAttribute($key);

                        $file->delete();
                    }
                }
            }
        });
    }

    /**
     * Instead of database column, return the FileField object.
     *
     * @param $key
     * @return FileField
     */
    public function getAttributeValue($key)
    {
        if (in_array($key, array_keys($this->files))) {
            return new FileField($this, $key);
        }

        return parent::getAttributeValue($key);
    }

    /**
     * Determine if it is a URL upload or file upload.
     * Upload the file and set file name
     *
     * @param $key
     * @param $value
     */
    public function setAttribute($key, $value)
    {
        if (! in_array($key, array_keys($this->files)) || is_null($value)) {
            parent::setAttribute($key, $value);
        }

        // Handle the values which are UploadedFile instances.
        else if ($value instanceof UploadedFile) {
            $name = Str::slug(pathinfo($value->getClientOriginalName(), PATHINFO_FILENAME));

            $extension = $value->getClientOriginalExtension();

            $fileName = join('.', array($name, $extension));

            //
            $fileField = new FileField($this, $key, $fileName);

            $this->attributes[$key] = $fileField->uploadFile($value);
        }

        // Handle the string values - files specified by path.
        else if (is_string($value)) {
            $name = pathinfo($value, PATHINFO_FILENAME);

            $extension = pathinfo($value, PATHINFO_EXTENSION);

            $fileName = join('.', array($name, $extension));

            //
            $fileField = new FileField($this, $key, $fileName);

            $this->attributes[$key] = $fileField->copyLocal($value);
        }
    }
}
