<?php

namespace Modules\Contacts\Controllers\Admin;

use Nova\Database\ORM\ModelNotFoundException;
use Nova\Support\Facades\Redirect;

use Modules\Contacts\Models\Contact;
use Modules\Contacts\Models\Message;
use Modules\Platform\Controllers\Admin\BaseController;

use ErrorException;


class Messages extends BaseController
{

    public function index($id)
    {
        try {
            $contact = Contact::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('contacts', 'Contact not found: #{0}', $id));
        }

        $messages = $contact->messages()->orderBy('created_at', 'DESC')->paginate(10);

        return $this->createView()
            ->shares('title', __d('contacts', 'Messages received by : {0}', $contact->name))
            ->with(compact('contact', 'messages'));
    }

    public function show($contactId, $id)
    {
        try {
            $contact = Contact::findOrFail($contactId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('contacts', 'Contact not found: #{0}', $contactId));
        }

        try {
            $message = Message::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts/' .$contact->id .'/messages')
                ->with('danger', __d('contacts', 'Message not found: #{0}', $id));
        }

        return $this->createView()
            ->shares('title', __d('contacts', 'Show Message'))
            ->with(compact('contact', 'message'));
    }

    public function destroy($contactId, $id)
    {
        try {
            $contact = Contact::findOrFail($contactId);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::back()->with('danger', __d('contacts', 'Contact not found: #{0}', $contactId));
        }

        try {
            $message = Message::findOrFail($id);
        }
        catch (ModelNotFoundException $e) {
            return Redirect::to('admin/contacts/' .$contact->id .'/messages')
                ->with('danger', __d('contacts', 'Message not found: #{0}', $id));
        }

        // Delete the Message and its attachments.
        $message->attachments->each(function ($attachment)
        {
            $attachment->delete();
        });

        $message->delete();

        // Update the Contact's messages count.
        $contact->updateCount();

        return Redirect::to('admin/contacts/' .$contact->id .'/messages')
            ->with('success', __d('contacts', 'The Message was successfully deleted.'));
    }
}
