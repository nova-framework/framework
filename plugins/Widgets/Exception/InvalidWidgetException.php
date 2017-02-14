<?php

namespace Plugins\Widgets\Exception;

use Exception;


class InvalidWidgetException extends Exception
{
    protected $message = 'Widget class must be an instance of Plugins\Widget\Widget';
}
