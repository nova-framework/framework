<?php

namespace Template;

use View\Engines\EngineInterface;
use View\Factory as ViewFactory;
use View\View;


class Template extends View
{

    public function __construct(ViewFactory $factory, EngineInterface $engine, $view, $path, $data = array())
    {
        parent::__construct($factory, $engine, $view, $path, $data);
    }

}
