<?php

namespace Modules\Contacts\Blocks;

use Nova\Support\Facades\Cache;
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

        $contact = Cache::section('contacts')->remember('block|' .$path, 1440, function () use ($path)
        {
            return Contact::findByPath($path);
        });

        Cache::tags('contacts', 'blocks')->put('test', 'contacts, blocks', 15);

        if (! is_null($contact)) {
            return View::make('Modules/Contacts::Blocks/Message', compact('contact', 'path'))->render();
        }
    }
}
