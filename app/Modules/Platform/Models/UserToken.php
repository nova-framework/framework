<?php

namespace App\Modules\Platform\Models;

use Nova\Database\ORM\Model as BaseModel;
use Nova\Support\Str;


class UserToken extends BaseModel
{
    protected $table = 'user_tokens';

    protected $primaryKey = 'id';

    protected $fillable = array('email', 'token');


    /**
     * Returns the User that belongs to this Token.
     */
    public function user()
    {
        return $this->belongsTo('App\Modules\Users\Models\User', 'email', 'email');
    }

    public static function uniqueToken()
    {
        $tokens = static::lists('token');

        do {
            $token = Str::random(100);
        }
        while (in_array($token, $tokens));

        return $token;
    }
}
