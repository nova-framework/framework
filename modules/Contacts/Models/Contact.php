<?php

namespace Modules\Contacts\Models;

use Nova\Database\ORM\Model as BaseModel;
use Nova\Support\Str;


class Contact extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'contacts';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = array('name', 'email', 'description', 'path');

    /**
     * @var array
     */
    protected $with = array('fieldGroups');


    /**
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function fieldGroups()
    {
        return $this->hasMany('Modules\Contacts\Models\FieldGroup', 'contact_id');
    }

    /**
     * @return \Nova\Database\ORM\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany('Modules\Contacts\Models\Message', 'contact_id');
    }

    /**
     * Listen to ORM events.
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function (Contact $model)
        {
            $model->load('messages');

            $model->messages->each(function ($message)
            {
                $message->delete();
            });
        });
    }

    public static function findByPath($path)
    {
        $contacts = static::all();

        foreach ($contacts as $contact) {
            if ($contact->matches($path)) {
                return $contact;
            }
        }

        return $contacts->first();
    }

    protected function matches($path)
    {
        if (empty($pattern = $this->getAttribute('path'))) {
            $patterns = array('*');
        } else {
            $lines = explode("\n", str_replace('<front>', '/', $pattern));

            $patterns = array_filter(array_map('trim', $lines), function ($value)
            {
                return ! empty($value);
            });
        }

        foreach ($patterns as $pattern) {
            if (Str::is($pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Update the comment count field.
     */
    public function updateCount()
    {
        $this->count = $this->messages()->count();

        $this->save();
    }
}
