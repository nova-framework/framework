<?php

namespace Modules\Contacts\Blocks;

use Nova\Support\Facades\Request;
use Nova\Support\Facades\View;

use Modules\Contacts\Models\Contact;
use Modules\Content\Blocks\Block;

use LogicException;


class Message extends Block
{

    public function render()
    {
        $path = Request::path();

        if (is_null($contact = Contact::findByPath($path))) {
            throw new LogicException('Contact not found');
        }

        return View::make('Modules/Contacts::Blocks/Message', compact('contact', 'path'))->render();
    }
}
