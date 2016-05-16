<?php

namespace App\Events;

class Test
{
    public static function handle($data)
    {
        echo '<pre>' .var_export($data, true) .'</pre>';
    }
}
