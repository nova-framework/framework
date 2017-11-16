<?php

namespace App\Modules\Content\Support\Contracts;

use Thunder\Shortcode\Shortcode\ShortcodeInterface;


interface ShortcodeInterface
{
    /**
     * @param ShortcodeInterface $shortcode
     * @return string
     */
    public function render(ShortcodeInterface $shortcode);
}
