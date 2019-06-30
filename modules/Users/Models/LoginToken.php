<?php

namespace Modules\Users\Models;

use Nova\Database\ORM\Model as BaseModel;
use Nova\Support\Str;


class UserLoginToken extends BaseModel
{
    protected $table = 'user_login_tokens';

    protected $primaryKey = 'id';

    protected $fillable = array('email', 'token');


    /**
     * Returns the User that belongs to this Token.
     */
    public function user()
    {
        return $this->belongsTo('Modules\Users\Models\User', 'email', 'email');
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
