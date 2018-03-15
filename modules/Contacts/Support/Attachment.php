<?php

namespace Modules\Contacts\Support;

use Nova\Support\Facades\File;
use Nova\Support\Facades\Log;
use Nova\Support\Str;

use Exception;
use JsonSerializable;


class Attachment implements JsonSerializable
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $path;

    /**
     * Where we store the uploaded files.
     *
     * @var string
     */
    const PATH = STORAGE_PATH .'files' .DS .'contacts' .DS .'attachments';


    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->size = $data['size'];
        $this->type = $data['type'];
        $this->path = $data['path'];
    }

    /**
     * Delete the specified file with the errors logging.
     *
     * @param string $path
     * @return void
     */
    protected static function delete()
    {
        try {
            File::delete($this->path);
        }
        catch (Exception $e) {
            Log::error($e->getMessage());
        }

        $this->path = null;
    }

    public function exists()
    {
        return ! empty($this->path);
    }

    public function url($download = false)
    {
        if (! empty($path = $this->getPath()) && File::exists($path)) {
            list ($token, $fileName) = explode('-', basename($path), 2);

            $method = $download ? 'download' : 'preview';

            return site_url('contacts/' .$method .'/' .$token .'/' .$fileName);
        }
    }

    public function previewable()
    {
        if (empty($type = $this->getMimeType()) {
            return false;
        } else if (($type == 'application/pdf') || Str::is('image/*', $type)) {
            return true;
        }

        return false;
    }

    public function jsonSerialize()
    {
        if (! $this->exists()) {
            return array('error' => 'File does not exist!');
        }

        return array(
            'name' => $this->name,
            'path' => $this->path,
            'size' => $this->size,
            'type' => $this->type,
        );
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getMimeType()
    {
        return $this->type;
    }

    public function getPath()
    {
        return $this->path;
    }
}
