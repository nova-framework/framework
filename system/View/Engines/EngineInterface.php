<?php

namespace View\Engines;


interface EngineInterface
{

    /**
     * Get the evaluated contents of the view.
     *
     * @param  string  $path
     * @param  array   $data
     * @return string
     */
    public function render($path, array $data = array());

}
