<?php

namespace App\Modules\Contacts\Blocks;

use Nova\Support\Facades\App;
use Nova\Support\Facades\Request;
use Nova\Support\Facades\View;

use App\Modules\Contacts\Models\Contact;
use App\Modules\Content\Blocks\Block;
use App\Modules\Content\Traits\ShortcodesTrait;

use Thunder\Shortcode\Shortcode\ShortcodeInterface as Shortcode;


class Message extends Block
{
    use ShortcodesTrait;


    public function render()
    {
        $path = Request::path();

        if (is_null($contact = Contact::findByPath($path))) {
            App::abort(500);
        }

        $this->addShortcode('input', function (Shortcode $shortcode)
        {
            return View::make('Shortcodes/Input', compact('shortcode'), 'Contacts');
        });

        $this->addShortcode('textarea', function (Shortcode $shortcode)
        {
            return View::make('Shortcodes/Textarea', compact('shortcode'), 'Contacts');
        });

        $content = $this->stripShortcodes($contact->content);

        return View::make('Blocks/Message', compact('path', 'content'), 'Contacts')->render();
    }
}
