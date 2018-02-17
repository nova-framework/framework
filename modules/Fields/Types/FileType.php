<?php

namespace Modules\Fields\Types;

use Nova\Http\UploadedFile;
use Nova\Support\Facades\File;
use Nova\Support\Facades\Log;
use Nova\Support\Facades\View;
use Nova\Support\Str;

use Modules\Fields\Types\Type as BaseType;

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
    protected $path = BASEPATH .'assets' .DS .'files';


    /**
     * Execute the cleanup when MetaData instance is saved or deleted.
     *
     * @param bool $force
     * @return string
     */
    public function cleanup($force = false)
    {
        if (empty($path = $this->model->getOriginal('value'))) {
            return;
        }

        // We have a valid file path.
        else if (($path == $this->model->getAttribute('value')) && ! $force) {
            return;
        }

        try {
            File::delete($path);
        }
        catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Gets a rendered form of the value.
     *
     * @param array $data
     * @return string
     */
    public function render(array $data = array())
    {
        $path = str_replace(BASEPATH, '', $this->model->value);

        return View::make('Fields/File', compact('path'), 'Fields')->with($data)->render();
    }

    /**
     * Gets the path where we store the uploaded files.
     *
     * @var string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Parse & set the meta item value.
     *
     * @param string $value
     */
    public function set($value)
    {
        if ($value instanceof UploadedFile) {
            $fileName = pathinfo($value->getClientOriginalName(), PATHINFO_FILENAME);

            $extension = $value->getClientOriginalExtension();

            $fileName = sprintf('%s-%s.%s', uniqid(), Str::slug($fileName), $extension);
        } else if (empty($value)) {
            return;
        }

        // An invalid value was given.
        else {
            throw new InvalidArgumentException("No uploaded file was given as value. [$value].");
        }

        $path = $this->getPath();

        if (! is_null($model = $this->getModel())) {
            $path .= DS .Str::plural($model->key);
        }

        $filePath = $path .DS .$fileName;

        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        // Move the uploaded file to the final location.
        $value->move($path, $fileName);

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
        return $value instanceof UploadedFile;
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
