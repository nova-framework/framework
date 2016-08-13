<?php

namespace Support\Contracts;


interface RenderableInterface
{
    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render();

    /**
     * Return true if the current Renderable instance is a Layout.
     *
     * @return bool
     */
    public function isLayout();
}
