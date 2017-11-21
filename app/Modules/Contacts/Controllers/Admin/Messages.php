<?php

namespace App\Modules\Contacts\Controllers\Admin;

use Nova\Support\Facades\Redirect;

use App\Modules\Contacts\Models\Contact;
use App\Modules\Contacts\Models\Message;
use App\Modules\Content\Traits\ShortcodesTrait;

use App\Modules\Platform\Controllers\Admin\BaseController;

use Thunder\Shortcode\Shortcode\ShortcodeInterface as Shortcode;

use ErrorException;


class Messages extends BaseController
{
    use ShortcodesTrait;


    public function index($id)
    {
        try {
            $contact = Contact::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Contact not found: #{0}', $id), 'danger');
        }

        $shortcodes = $this->parseShortcodes($contact->content);

        //
        $labels = array();

        foreach ($shortcodes as $shortcode) {
            if ($shortcode->hasParameter('name')) {
                $name = $shortcode->getParameter('name');
            } else {
                throw new ErrorException('Invalid shorcode.');
            }

            if ($shortcode->hasParameter('label')) {
                $labels[$name] = $shortcode->getParameter('label');
            } else {
                throw new ErrorException('Invalid shorcode.');
            }
        }

        $messages = $contact->messages()->orderBy('created_at', 'DESC')->paginate(15);

        return $this->createView()
            ->shares('title', __d('contacts', 'Messages received by : {0}', $contact->name))
            ->with(compact('contact', 'messages', 'labels'));
    }

    public function destroy($contactId, $id)
    {
        try {
            $message = Message::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Message not found: #{0}', $id), 'danger');
        }

        $contact = $message->contact()->first();

        // Delete the Message.
        $message->delete();

        // Update the Contact's messages count.
        $contact->updateCount();

        return Redirect::back()
            ->withStatus(__d('content', 'The Message was successfully deleted.'), 'success');
    }
}
