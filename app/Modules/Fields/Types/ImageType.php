<?php

namespace App\Modules\Fields\Types;

use Nova\Http\UploadedFile;
use Nova\Support\Facades\View;
use Nova\Support\Str;

use App\Modules\Fields\Types\FileType as BaseType;

use Exception;
use InvalidArgumentException;


class ImageType extends BaseType
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
    protected $path = ROOTDIR .'assets' .DS .'images';


    /**
     * Gets a rendered form of the value.
     *
     * @return string
     */
    public function render()
    {
        $path = str_replace(ROOTDIR, '', $this->get());

        return View::make('Fields/Image', compact('path'), 'Fields')->render();
    }

    /**
     * Assertain whether we can handle the Field of variable passed.
     *
     * @param  mixed  $value
     * @return bool
     */
    public function isType($value)
    {
        return ($value instanceof UploadedFile) && Str::is('image/*', $value->getMimeType());
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
