<?php

namespace Shared\Database\ORM\FileField;

use Nova\Database\ORM\Model;
use Nova\Support\Facades\App;
use Nova\Support\Facades\Log;
use Nova\Support\Str;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Exception;
use JsonSerializable;


class FileField implements JsonSerializable
{
    /**
     * @var Nova\Database\ORM\Model The ORM model this field is in
     */
    protected $model;

    /**
     * @var \Nova\Filesystem\Filesystem The file system which handles files for this field
     */
    protected $files;

    /**
     * @var string Name of this field
     */
    protected $key;

    /**
     * @var array Disk and path config for this field
     */
    protected $options;

    /**
     * @var string path to the file on the disk
     */
    protected $path;

    /**
     * @var string
     */
    protected $fileName;


    /**
     * FileField constructor.
     *
     * @param Model $model
     * @param $key
     * @param null $fileName
     */
    public function __construct(Model $model, $key, $fileName = null)
    {
        $config = App::make('config');

        // If filename wasn't given, take it from the model
        if ($model->exists) {
            $attributes = $model->getAttributes();

            $this->path = isset($attributes[$key]) ? $attributes[$key] : null;

            if (isset($this->path) && is_null($fileName)) {
                $fileName = pathinfo($this->path, PATHINFO_FILENAME);
            }
        }

        $this->model = $model;

        $this->key = $key;

        $this->options = array_merge(
            $config->get('fileField', array()),
            $this->model->files[$key]
        );

        $this->fileName = $fileName;

        $this->files = App::make('files');
    }

    /**
     * Substitute placeholders and return the path for the file
     *
     * @return string
     */
    protected function getPathForUpload()
    {
        $extension = pathinfo($this->fileName, PATHINFO_EXTENSION);

        //
        $className = class_basename($this->model);

        $classSlug = Str::slug(Str::snake(Str::plural($className)));

        //
        $search = array(':extension', ':attribute', ':unique_id', ':class_slug', ':file_name');

        $replace = array(
            $extension,
            $this->key,
            uniqid(),
            $classSlug,
            $this->fileName
        );

        return str_replace($search, $replace, $this->options['path']);
    }

    /**
     * @return \Nova\Filesystem\Filesystem
     */
    public function getFileSystem()
    {
        return $this->files;
    }

    /**
     * Move the given file to appropriate directory
     *
     * @param UploadedFile $file
     * @return mixed
     */
    public function uploadFile(UploadedFile $file)
    {
        $path = $this->getPathForUpload();

        $this->files->makeDirectory(dirname($path), 0755, true, true);

        if ($this->files->put($path, fopen($file->getRealPath(), 'r+'))) {
            return $this->path = $path;
        }
    }

    public function copyLocal($currentPath)
    {
        $path = $this->getPathForUpload();

        $this->files->makeDirectory(dirname($path), 0755, true, true);

        if ($this->files->copy($currentPath, $path)) {
            return $this->path = $path;
        }
    }

    /**
     * Delete the file
     *
     * @return mixed
     */
    public function delete()
    {
        try {
            return $this->files->delete($this->path);
        }

        // Catch all exceptions.
        catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function exists()
    {
        return ! empty($this->path);
    }

    /**
     * Delegate properties to filesystem
     *
     * @param $name
     * @return null|string
     */
    public function __get($name)
    {
        switch ($name) {
            case 'path':
                return $this->path;

            case 'name':
                return $this->fileName;

            default:
                return $this->files->$name;
        }
    }

    /**
     * Delegate methods to filesystem
     *
     * @param $name
     * @param $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        // Prepend filename to the arguments.
        array_unshift($args, $this->path);

        return call_user_func_array(array($this->files, $name), $args);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        if (! $this->exists()) {
            return array('error' => 'File does not exist!');
        }

        try {
            return array(
                'name' => $this->fileName,
                'path' => $this->path,
                'size' => $this->size(),
                'type' => $this->getMimetype()
            );
        }

        // Catch all exceptions.
        catch (Exception $e) {
            return array('error' => $e->getMessage());
        }
    }

    public function __toString()
    {
        return $this->path ?: $this->options['defaultPath'];
    }
}
