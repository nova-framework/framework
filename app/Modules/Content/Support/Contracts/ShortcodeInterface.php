<?php

namespace App\Modules\Content\Support\Contracts;

use Thunder\Shortcode\Shortcode\ShortcodeInterface as Shortcode;


interface ShortcodeInterface extends Shortcode
{
    /**
     * @param ShortcodeInterface $shortcode
     * @return string
     */
    public function render(ShortcodeInterface $shortcode);
}
