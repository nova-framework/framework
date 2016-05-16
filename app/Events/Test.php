<?php

namespace App\Events;

class Test
{
    public static function handle($data)
    {
        return '<pre>' .var_export($data, true) .'</pre>';
    }
}
