<?php

namespace App\Events;


class Test
{
    public static function handle($message, $params = array())
    {
        echo '<pre>' .var_export($message, true) .'</pre>';
        echo '<pre>' .var_export($params, true) .'</pre>';
    }
}
