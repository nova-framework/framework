<?php

namespace App\Modules\Demos\Events;

class Test
{
    public function handle($data)
    {
        return '<pre>' .str_replace('::', '@', __METHOD__) .' : ' .var_export($data, true) .'</pre>';
    }
}
