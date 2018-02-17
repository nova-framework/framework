<?php

namespace Modules\Contacts\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Redirect;

use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\Message;
use Modules\Content\Traits\ShortcodesTrait;

use Modules\Platform\Controllers\Admin\BaseController;

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

        $elements = $this->getMessageElements($contact);

        $messages = $contact->messages()->orderBy('created_at', 'DESC')->paginate(10);

        return $this->createView()
            ->shares('title', __d('contacts', 'Messages received by : {0}', $contact->name))
            ->with(compact('contact', 'messages', 'elements'));
    }

    public function show($cid, $mid)
    {
        try {
            $contact = Contact::findOrFail($cid);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Contact not found: #{0}', $cid), 'danger');
        }

        $elements = $this->getMessageElements($contact);

        try {
            $message = Message::findOrFail($mid);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->withStatus(__d('content', 'Message not found: #{0}', $mid), 'danger');
        }

        return $this->createView()
            ->shares('title', __d('contacts', 'Show Message'))
            ->with(compact('contact', 'message', 'elements'));
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

    protected function getMessageElements(Contact $contact)
    {
        $shortcodes = $this->parseShortcodes($contact->message);

        //
        $elements = array();

        foreach ($shortcodes as $shortcode) {
            $type = $shortcode->getName();

            if (($type == 'input') && ($shortcode->getParameter('type') == 'submit')) {
                continue;
            }

            // Not a submit button.
            else if ($type == 'option') {
                $name = $shortcode->getParameter('value');
            }

            // The shortcode should have a name parameter.
            else if ($shortcode->hasParameter('name')) {
                $name = 'contact_' .$shortcode->getParameter('name');
            } else {
                throw new ErrorException('Invalid shortcode.');
            }

            // The shortcode should have a label.
            if ($shortcode->hasParameter('label')) {
                $label = $shortcode->getParameter('label');
            } else {
                throw new ErrorException('Invalid shortcode.');
            }

            $elements[$name] = compact('type', 'label');
        }

        return $elements;
    }
}
