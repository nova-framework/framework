<?php

namespace App\Events;

class Test
{
    public static function handle($data)
    {
        return '<pre>App\Events\Test@hande : ' .var_export($data, true) .'</pre>';
    }
}
