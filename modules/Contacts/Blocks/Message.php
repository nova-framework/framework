<?php

namespace Modules\Contacts\Blocks;

use Nova\Support\Facades\App;
use Nova\Support\Facades\Request;
use Nova\Support\Facades\View;

use Modules\Contacts\Models\Contact;
use Modules\Content\Blocks\Block;
use Modules\Content\Traits\ShortcodesTrait;

use Thunder\Shortcode\Shortcode\ShortcodeInterface as Shortcode;

use ErrorException;


class Message extends Block
{
    use ShortcodesTrait;


    public function render()
    {
        $path = Request::path();

        if (is_null($contact = Contact::findByPath($path))) {
            throw new ErrorException('Contact not found');
        }

        $this->addShortcode('input', function (Shortcode $shortcode)
        {
            return View::make('Modules/Contacts::Shortcodes/Input', compact('shortcode'));
        });

        $this->addShortcode('textarea', function (Shortcode $shortcode)
        {
            return View::make('Modules/Contacts::Shortcodes/Textarea', compact('shortcode'));
        });

        $this->addShortcode('select', function (Shortcode $shortcode)
        {
            return View::make('Modules/Contacts::Shortcodes/Select', compact('shortcode'));
        });

        $this->addShortcode('option', function (Shortcode $shortcode)
        {
            return View::make('Modules/Contacts::Shortcodes/Option', compact('shortcode'));
        });

        $content = $this->stripShortcodes($contact->message);

        return View::make('Modules/Contacts::Blocks/Message', compact('path', 'content'))->render();
    }
}
