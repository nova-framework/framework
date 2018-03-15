<?php

namespace Modules\Contacts\Models;

use Nova\Database\ORM\Model as BaseModel;
use Nova\Support\Facades\File;
use Nova\Support\Facades\Log;
use Nova\Support\Str;

use Modules\Contacts\Models\FieldItem;
use Modules\Contacts\Support\Attachment;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use LogicException;


class CustomField extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'contact_custom_fields';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = array('message_id', 'field_item_id', 'type', 'name', 'value');


    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function message()
    {
        return $this->belongsTo('Modules\Contacts\Models\Message', 'message_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\BelongsTo
     */
    public function fieldItem()
    {
        return $this->belongsTo('Modules\Contacts\Models\FieldItem', 'field_item_id');
    }

    /**
     * Listen to ORM events.
     *
     * Cleanup properly on update and delete
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function (CustomField $model)
        {
            if ($model->getAttributeValue('type') != 'file') {
                return;
            }

            // No file to delete when the original value is empty.
            else if (empty($value = $model->getOriginal('value'))) {
                return;
            }

            // We will delete the previous file on path changes.
            else if ($value != $model->getAttributeFromArray('value')) {
                $attachment = $model->getAttributeValue('value');

                $attachment->delete();
            }
        });

        static::deleting(function (CustomField $model)
        {
            // Don't delete the file if you are doing a soft delete!
            if (method_exists($model, 'restore') && ! $model->forceDeleting) {
                return;
            }

            // If there is a file path specified, we will delete it.
            else if ($model->getAttributeValue('type') == 'file') {
                $attachment = $model->getAttributeValue('value');

                $attachment->delete();
            }
        });
    }

    /**
     * Handle the UploadedFile and create a new record.
     *
     * @param UploadedFile $file
     * @param Modules\Contacts\Models\FieldItem $item
     * @return \Modules\Contacts\Models\CustomField
     * @throws \LogicException
     */
    public static function uploadFileAndCreate(UploadedFile $file, FieldItem $item)
    {
        if ($item->type != 'file') {
            throw new LogicException('The FieldItem should be of type [file].');
        }

        if (! File::exists($path = Attachment::PATH)) {
            File::makeDirectory($path, 0755, true, true);
        }

        $fileName = pathinfo($name = $file->getClientOriginalName(), PATHINFO_FILENAME);

        $path = sprintf('%s/%s-%s.%s',
            $path, uniqid(), Str::slug($fileName), $file->guessClientExtension()
        );

        if (! File::put($path, fopen($file->getRealPath(), 'r+'))) {
            throw new LogicException('Failed to move the uploaded file.');
        }

        return static::create(array(
            'name' => $item->name,
            'type' => $item->type,

            //
            'value' => array(
                'name' => $name,
                'size' => file->getSize(),
                'type' => $file->getClientMimeType(),
                'path' => $path,
            ),

            // Resolve the relationships.
            'field_item_id' => $item->id,

            // Will be updated later, when the model will be attached to parent.
            'message_id' => 0,
        ));
    }

    /**
     * @param  mixed  $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        try {
            $result = unserialize($value);

            if (($result === false) && ($value !== false)) {
                return $value;
            }

            $type = $this->getAttributeFromArray('type');

            if (($type == 'file') && is_array($result)) {
                return new Attachment($result);
            }

            return $result;
        }
        catch (Exception $e) {
            return $value;
        }
    }

    /**
     * @param  mixed  $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        if (is_array($value) || is_object($value)) {
            $value = serialize($value);
        }

        // When the value is a string containing serialized data, we should serialize it again.
        else if (is_string($value) && preg_match("#^((N;)|((a|O|s):[0-9]+:.*[;}])|((b|i|d):[0-9.E-]+;))$#um", $value)) {
            $value = serialize($value);
        }

        $this->attributes['value'] = $value;
    }
}
