<?php

namespace App\Modules\Fields\Types;

use Nova\Http\UploadedFile;
use Nova\Support\Facades\File;
use Nova\Support\Facades\Log;
use Nova\Support\Facades\View;
use Nova\Support\Str;

use App\Modules\Fields\Types\Type as BaseType;

use Exception;
use InvalidArgumentException;


class FileType extends BaseType
{
    /**
     * The type handled by this Type class.
     *
     * @var string
     */
    protected $type = 'file';

    /**
     * The partial View used for editor rendering.
     *
     * @var string
     */
    protected $view = 'Editor/File';

    /**
     * Where we store the uploaded files.
     *
     * @var string
     */
    protected $path = ROOTDIR .'assets' .DS .'files';


    /**
     * Execute the cleanup when MetaData instance is saved or deleted.
     *
     * @return string
     */
    public function cleanup($force = false)
    {
        if (empty($path = $this->model->getOriginal('value'))) {
            return;
        }

        // The file path is valid.
        else if (($path == $this->model->getAttribute('value')) && ! $force) {
            return;
        }

        try {
            File::delete($filePath);
        }

        // Catch all exceptions.
        catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Gets a rendered form of the value.
     *
     * @return string
     */
    public function render()
    {
        $path = str_replace(ROOTDIR, '', $this->model->value);

        return View::make('Fields/File', compact('path'), 'Fields')->render();
    }

    /**
     * Parse & set the meta item value.
     *
     * @param string $value
     */
    public function set($value)
    {
        if ($value instanceof UploadedFile) {
            $name = Str::slug(pathinfo($value->getClientOriginalName(), PATHINFO_FILENAME));

            $extension = $value->getClientOriginalExtension();
        } else if (File::exists($value)) {
            $name = pathinfo($value, PATHINFO_FILENAME);

            $extension = pathinfo($value, PATHINFO_EXTENSION);
        } else {
            throw new InvalidArgumentException("The value is not a file. [$value].");
        }

        $fileName = sprintf('%s-%s.%s', uniqid(), $name, $extension);

        $basePath = $this->path;

        if (! is_null($model = $this->getModel())) {
            $basePath .= DS .Str::plural($model->key);
        }

        $filePath = $basePath .DS .$fileName;

        // Ensure that exists the folder where the file will be stored.
        File::makeDirectory($basePath, 0755, true, true);

        if ($value instanceof UploadedFile) {
            File::put($filePath, fopen($value->path(), 'r+'));
        } else {
            File::copy($value, $filePath);
        }

        parent::set($filePath);
    }

    /**
     * Assertain whether we can handle the Field of variable passed.
     *
     * @param  mixed  $value
     * @return bool
     */
    public function isType($value)
    {
        if ($value instanceof UploadedFile) {
            return true;
        }

        return is_string($value) && File::exists($value);
    }

    /**
     * Output value to string.
     *
     * @return string
     */
    public function toString()
    {
        return $this->get();
    }
}
