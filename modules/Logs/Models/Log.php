<?php

namespace Modules\Logs\Models;

use Nova\Database\ORM\Model;


class Log extends Model
{
    protected $table = 'logs';

    protected $primaryKey = 'id';

    protected $fillable = array('user_id', 'group_id', 'url', 'message', 'data');


    public function group()
    {
        return $this->belongsTo('Modules\Logs\Models\LogGroup', 'group_id');
    }

    public function user()
    {
        return $this->hasOne('Modules\Users\User', 'id', 'user_id');
    }

    public function getDataAttribute($value) {
        return $this->maybeDecode($value);
    }

    public function setDataAttribute($value) {
        $this->attributes['data'] = $this->maybeEncode($value);
    }

    /**
     * Decode value only if it was encoded to JSON.
     *
     * @param  string  $original
     * @param  bool    $assoc
     * @return mixed
     */
    protected function maybeDecode($original, $assoc = true)
    {
        if (is_numeric($original)) return $original;

        $data = json_decode($original, $assoc);

        return (is_object($data) || is_array($data)) ? $data : $original;
    }

    /**
     * Encode data to JSON, if needed.
     *
     * @param  mixed  $data
     * @return mixed
     */
    protected function maybeEncode($data)
    {
        if (is_array($data) || is_object($data)) {
            return json_encode($data);
        }

        return $data;
    }
}
