<?php

namespace App\Events;

class Test
{
    public static function handle($data)
    {
        return '<pre>' .str_replace('::', '@', __METHOD__) .' : ' .var_export($data, true) .'</pre>';
    }
}
