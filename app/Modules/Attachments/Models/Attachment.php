<?php

namespace App\Modules\Attachments\Models;

use Nova\Database\ORM\Model as BaseModel;

use Shared\Database\ORM\FileField\FileFieldTrait;


class Attachment extends BaseModel
{
    use FileFieldTrait;

    //
    protected $table = 'attachments';

    protected $primaryKey = 'id';

    protected $fillable = array(
        'name', 'size', 'type', 'file', 'ownerable_id', 'ownerable_type', 'attachable_id', 'attachable_type'
    );

    public $files = array(
        'file' => array(
            'path'        => ROOTDIR .'files/attachments/:unique_id-:file_name',
            'defaultPath' => ROOTDIR .'files/attachments/users/no-file.png',
        ),
    );


    /**
     * Get the ownerable entity that the attachment belongs to.
     */
    public function ownerable()
    {
        return $this->morphTo();
    }

    /**
     * Get the attachable entity that the attachment belongs to.
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    public function path()
    {
        return $this->file->path;
    }

    public function name()
    {
        return $this->file->name;
    }

    public function url($download = false)
    {
        $file = $this->getAttribute('file');

        if ($file->exists()) {
            $method = $download ? 'download' : 'preview';

            list ($token, $filename) = explode('-', basename((string) $this->file->path), 2);

            return site_url('attachments/' .$method .'/' .$token .'/' .$filename);
        }
    }
}
