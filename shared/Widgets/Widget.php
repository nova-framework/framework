<?php

namespace Shared\Widgets;


abstract class Widget
{
    abstract public function render(array $parameters = array());
}
